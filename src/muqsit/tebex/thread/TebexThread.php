<?php

declare(strict_types=1);

namespace muqsit\tebex\thread;

use Exception;
use Generator;
use Logger;
use muqsit\tebexapi\connection\handler\TebexConnectionHandler;
use muqsit\tebexapi\connection\request\TebexRequest;
use muqsit\tebexapi\connection\request\TebexRequestHolder;
use muqsit\tebexapi\connection\response\TebexResponse;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\connection\response\TebexResponseHolder;
use muqsit\tebexapi\connection\SslConfiguration;
use muqsit\tebexapi\connection\TebexConnectionHelper;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use Threaded;
use function is_string;

final class TebexThread extends Thread{

	/**
	 * @var TebexResponseHandler[]
	 *
	 * @phpstan-var TebexResponseHandler<TebexResponse>[]
	 */
	private static array $handlers = [];

	private static int $handler_ids = 0;

	/** @var SleeperNotifier<mixed> */
	private SleeperNotifier $notifier;

	/** @var Threaded<string> */
	private Threaded $incoming;

	/** @var Threaded<string> */
	private Threaded $outgoing;

	private Logger $logger;
	public int $busy_score = 0;
	private bool $running = false;
	private string $secret;
	private string $ca_path;
	private string $_connection_handler;

	/**
	 * @param Logger $logger
	 * @param SleeperNotifier<mixed> $notifier
	 * @param string $secret
	 * @param SslConfiguration $ssl_config
	 * @param TebexConnectionHandler $connection_handler
	 */
	public function __construct(Logger $logger, SleeperNotifier $notifier, string $secret, SslConfiguration $ssl_config, TebexConnectionHandler $connection_handler){
		$this->_connection_handler = igbinary_serialize($connection_handler);

		$this->notifier = $notifier;
		$this->ca_path = $ssl_config->getCAInfoPath();
		$this->incoming = new Threaded();
		$this->outgoing = new Threaded();
		$this->logger = $logger;
		$this->secret = $secret;
	}

	/**
	 * @param TebexRequest $request
	 * @param TebexResponseHandler $handler
	 *
	 * @phpstan-template TTebexResponse of \muqsit\tebexapi\connection\response\TebexResponse
	 * @phpstan-param TebexRequest<TTebexResponse> $request
	 * @phpstan-param TebexResponseHandler<TTebexResponse> $handler
	 */
	public function push(TebexRequest $request, TebexResponseHandler $handler) : void{
		$handler_id = ++self::$handler_ids;
		$this->incoming[] = igbinary_serialize(new TebexRequestHolder($request, $handler_id));
		self::$handlers[$handler_id] = $handler;
		++$this->busy_score;
		$this->synchronized(function() : void{
			$this->notifyOne();
		});
	}

	protected function onRun() : void{
		$this->running = true;

		/** @var TebexConnectionHandler $connection_handler */
		$connection_handler = igbinary_unserialize($this->_connection_handler);

		$default_curl_opts = TebexConnectionHelper::buildDefaultCurlOptions($this->secret, $this->ca_path);
		while($this->running){
			while(($request_serialized = $this->incoming->shift()) !== null){
				assert(is_string($request_serialized));
				/** @var TebexRequestHolder $request_holder */
				$request_holder = igbinary_unserialize($request_serialized);
				$this->logger->debug("[cURL] Executing request: {$request_holder->request->getEndpoint()}");

				try{
					$response_holder = $connection_handler->handle($request_holder, $default_curl_opts);
				}catch(Exception $e){
					$this->logger->logException($e);
					throw $e;
				}

				$this->outgoing[] = igbinary_serialize($response_holder);
				$this->notifier->wakeupSleeper();
			}
			$this->sleep();
		}
	}

	public function sleep() : void{
		$this->synchronized(function() : void{
			if($this->running){
				$this->wait();
			}
		});
	}

	public function stop() : void{
		$this->running = false;
		$this->synchronized(function() : void{
			$this->notify();
		});
	}

	/**
	 * Collects all responses and returns the total latency
	 * (in seconds) in sending request and getting response.
	 *
	 * @return Generator<float>
	 */
	public function collectPending() : Generator{
		while(($holder_serialized = $this->outgoing->shift()) !== null){
			assert(is_string($holder_serialized));

			/** @var TebexResponseHolder<TebexResponse> $holder */
			$holder = igbinary_unserialize($holder_serialized);

			$holder->trigger(self::$handlers[$holder->handler_id]);
			unset(self::$handlers[$holder->handler_id]);
			--$this->busy_score;

			yield $holder->latency;
		}
	}
}
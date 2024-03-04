<?php

declare(strict_types=1);

namespace muqsit\tebex\thread;

use Exception;
use Generator;
use muqsit\tebexapi\connection\handler\TebexConnectionHandler;
use muqsit\tebexapi\connection\request\TebexRequest;
use muqsit\tebexapi\connection\request\TebexRequestHolder;
use muqsit\tebexapi\connection\response\TebexResponse;
use muqsit\tebexapi\connection\response\TebexResponseFailureHolder;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\connection\response\TebexResponseHolder;
use muqsit\tebexapi\connection\SslConfiguration;
use muqsit\tebexapi\connection\TebexConnectionHelper;
use muqsit\tebexapi\utils\TebexException;
use pmmp\thread\ThreadSafeArray;
use pocketmine\snooze\SleeperHandlerEntry;
use pocketmine\thread\log\ThreadSafeLogger;
use pocketmine\thread\Thread;
use function is_string;

final class TebexThread extends Thread{

	/** @var array<int, TebexResponseHandler<TebexResponse>> */
	private static array $handlers = [];

	private static int $handler_ids = 0;

	readonly private SleeperHandlerEntry $sleeper_handler_entry;

	/** @var ThreadSafeArray<string> */
	private ThreadSafeArray $incoming;

	/** @var ThreadSafeArray<string> */
	private ThreadSafeArray $outgoing;

	readonly private ThreadSafeLogger $logger;
	public int $busy_score = 0;
	private bool $running = false;
	readonly private string $secret;
	readonly private string $ca_path;
	readonly private string $_connection_handler;

	/**
	 * @param ThreadSafeLogger $logger
	 * @param SleeperHandlerEntry $sleeper_handler_entry
	 * @param string $secret
	 * @param SslConfiguration $ssl_config
	 * @param TebexConnectionHandler $connection_handler
	 */
	public function __construct(ThreadSafeLogger $logger, SleeperHandlerEntry $sleeper_handler_entry, string $secret, SslConfiguration $ssl_config, TebexConnectionHandler $connection_handler){
		$this->_connection_handler = igbinary_serialize($connection_handler);

		$this->sleeper_handler_entry = $sleeper_handler_entry;
		$this->ca_path = $ssl_config->getCAInfoPath();
		$this->incoming = new ThreadSafeArray();
		$this->outgoing = new ThreadSafeArray();
		$this->logger = $logger;
		$this->secret = $secret;
	}

	/**
	 * @template TTebexResponse of TebexResponse
	 * @param TebexRequest<TTebexResponse> $request
	 * @param TebexResponseHandler<TTebexResponse> $handler
	 */
	public function push(TebexRequest $request, TebexResponseHandler $handler) : void{
		$handler_id = ++self::$handler_ids;
		$this->incoming[] = igbinary_serialize(new TebexRequestHolder($request, $handler_id));
		self::$handlers[$handler_id] = $handler;
		++$this->busy_score;
		$this->synchronized($this->notify(...));
	}

	protected function onRun() : void{
		$this->running = true;

		$notifier = $this->sleeper_handler_entry->createNotifier();

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
				}catch(TebexException $e){
					$response_holder = new TebexResponseFailureHolder($request_holder->handler_id, $e->getLatency(), $e->getMessage(), $e->getCode(), $e->getTraceAsString());
				}catch(Exception $e){
					$response_holder = new TebexResponseFailureHolder($request_holder->handler_id, 5000, $e->getMessage(), $e->getCode(), $e->getTraceAsString());
				}

				$this->outgoing[] = igbinary_serialize($response_holder);
				$notifier->wakeupSleeper();
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
		$this->synchronized($this->notify(...));
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
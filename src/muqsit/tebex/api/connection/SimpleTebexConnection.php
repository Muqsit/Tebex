<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection;

use Closure;
use muqsit\tebex\api\connection\handler\SimpleTebexConnectionHandler;
use muqsit\tebex\api\connection\handler\TebexConnectionHandler;
use muqsit\tebex\api\connection\request\TebexRequest;
use muqsit\tebex\api\connection\request\TebexRequestHolder;
use muqsit\tebex\api\connection\response\TebexResponse;
use muqsit\tebex\api\connection\response\TebexResponseHandler;
use RuntimeException;
use SplQueue;

/**
 * A simple blocking-IO implementation of TebexConnection.
 */
final class SimpleTebexConnection implements TebexConnection{

	public static function create(string $secret, ?SSLConfiguration $configuration = null, ?TebexConnectionHandler $handler = null) : self{
		$configuration ??= SSLConfiguration::empty();
		$handler ??= new SimpleTebexConnectionHandler();

		$instance = new self($handler, TebexConnectionHelper::buildDefaultCurlOptions($secret, $configuration->getCAInfoPath()));
		$instance->registerDisconnectCallback(static function() use($configuration) : void{ $configuration->close(); });
		return $instance;
	}

	private TebexConnectionHandler $handler;
	private int $handler_count = 0;
	private float $latency = 0.0;

	/** @var Closure[] */
	private array $disconnect_callbacks = [];

	/**
	 * @var mixed[]
	 *
	 * @phpstan-var array<int, mixed>
	 */
	private array $default_curl_options;

	/** @phpstan-var SplQueue<TebexRequestHolder>  */
	private SplQueue $queued;

	/**
	 * @var TebexResponseHandler[]
	 *
	 * @phpstan-var array<int, TebexResponseHandler<TebexResponse>>
	 */
	private array $callbacks = [];

	/**
	 * @param TebexConnectionHandler $handler
	 * @param mixed[] $default_curl_options
	 *
	 * @phpstan-param array<int, mixed> $default_curl_options
	 */
	public function __construct(TebexConnectionHandler $handler, array $default_curl_options){
		$this->queued = new SplQueue();
		$this->handler = $handler;
		$this->default_curl_options = $default_curl_options;
	}

	/**
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure() : void $callback
	 */
	public function registerDisconnectCallback(Closure $callback) : void{
		$this->disconnect_callbacks[] = $callback;
	}

	public function request(TebexRequest $request, TebexResponseHandler $callback) : void{
		$request_holder = new TebexRequestHolder($request, ++$this->handler_count);
		$this->queued->enqueue($request_holder);
		$this->callbacks[$request_holder->handler_id] = $callback;
	}

	public function getLatency() : float{
		return $this->latency;
	}

	public function process() : void{
		while(true){
			try{
				$request_holder = $this->queued->dequeue();
			}catch(RuntimeException $_){
				break;
			}

			$response_holder = $this->handler->handle($request_holder, $this->default_curl_options);
			$response_holder->trigger($this->callbacks[$request_holder->handler_id]);
			unset($this->callbacks[$request_holder->handler_id]);

			$this->latency = $response_holder->latency;
		}
	}

	public function wait() : void{
		while(!$this->queued->isEmpty()){
			$this->process();
		}
	}

	public function disconnect() : void{
		foreach($this->disconnect_callbacks as $callback){
			$callback();
		}
		$this->disconnect_callbacks = [];
	}
}
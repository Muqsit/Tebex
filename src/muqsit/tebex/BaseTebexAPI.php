<?php

declare(strict_types=1);

namespace muqsit\tebex;

use Logger;
use muqsit\tebex\api\connection\handler\SimpleTebexConnectionHandler;
use muqsit\tebex\api\connection\request\TebexRequest;
use muqsit\tebex\api\connection\response\TebexResponseHandler;
use muqsit\tebex\api\connection\SSLConfiguration;
use muqsit\tebex\thread\TebexThread;
use muqsit\tebex\thread\TebexThreadPool;

abstract class BaseTebexAPI{

	public const BASE_ENDPOINT = "https://plugin.tebex.io";

	private TebexThreadPool $pool;
	private SSLConfiguration $ssl_config;

	public function __construct(Logger $logger, string $secret, SSLConfiguration $ssl_config, int $workers){
		$this->pool = new TebexThreadPool(new SimpleTebexConnectionHandler());
		$this->ssl_config = $ssl_config;
		for($i = 0; $i < $workers; $i++){
			$this->pool->addWorker(new TebexThread($logger, $this->pool->getNotifier(), $secret, $ssl_config, $this->pool->getConnectionHandler()));
		}
		$this->pool->start();
	}

	/**
	 * @param TebexRequest $request
	 * @param TebexResponseHandler $callback
	 *
	 * @phpstan-template TTebexResponse of \muqsit\tebex\api\connection\response\TebexResponse
	 * @phpstan-param TebexRequest<TTebexResponse> $request
	 * @phpstan-param TebexResponseHandler<TTebexResponse> $callback
	 */
	public function request(TebexRequest $request, TebexResponseHandler $callback) : void{
		$this->pool->getLeastBusyWorker()->push($request, $callback);
	}

	public function getLatency() : float{
		return $this->pool->getLatency();
	}

	public function waitAll(int $sleep_duration_ms = 50000) : void{
		$this->pool->waitAll($sleep_duration_ms);
	}

	public function shutdown() : void{
		$this->pool->shutdown();
		$this->ssl_config->close();
	}
}
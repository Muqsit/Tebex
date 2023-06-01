<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use muqsit\tebexapi\connection\handler\SimpleTebexConnectionHandler;
use muqsit\tebexapi\connection\request\TebexRequest;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\connection\SslConfiguration;
use muqsit\tebexapi\connection\TebexConnection;
use muqsit\tebex\thread\TebexThread;
use muqsit\tebex\thread\TebexThreadPool;
use pocketmine\Server;
use pocketmine\thread\log\ThreadSafeLogger;
use RuntimeException;

final class ThreadedTebexConnection implements TebexConnection{

	readonly private TebexThreadPool $pool;
	readonly private SslConfiguration $ssl_config;

	public function __construct(ThreadSafeLogger $logger, string $secret, SslConfiguration $ssl_config, int $workers){
		$this->pool = new TebexThreadPool(new SimpleTebexConnectionHandler());
		$this->ssl_config = $ssl_config;

		$class_loaders = [];
		$devirion = Server::getInstance()->getPluginManager()->getPlugin("DEVirion");
		if($devirion !== null){
			if(!method_exists($devirion, "getVirionClassLoader")){
				throw new RuntimeException();
			}
			$class_loaders[] = Server::getInstance()->getLoader();
			$class_loaders[] = $devirion->getVirionClassLoader();
		}

		for($i = 0; $i < $workers; $i++){
			$thread = new TebexThread($logger, $this->pool->sleeper_handler_entry, $secret, $ssl_config, $this->pool->connection_handler);
			if(count($class_loaders) > 0){
				$thread->setClassLoaders($class_loaders);
			}
			$this->pool->addWorker($thread);
		}
		$this->pool->start();
	}

	public function request(TebexRequest $request, TebexResponseHandler $callback) : void{
		$this->pool->getLeastBusyWorker()->push($request, $callback);
	}

	public function getLatency() : float{
		return $this->pool->getLatency();
	}

	public function waitAll(int $sleep_duration_ms = 50000) : void{
		$this->pool->waitAll($sleep_duration_ms);
	}

	public function process() : void{
		// NOOP, done on child thread
	}

	public function wait() : void{
		$this->pool->waitAll(50_000);
	}

	public function disconnect() : void{
		$this->pool->shutdown();
		$this->ssl_config->close();
	}
}
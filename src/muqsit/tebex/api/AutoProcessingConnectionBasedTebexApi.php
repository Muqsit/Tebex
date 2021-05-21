<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

use muqsit\tebex\api\connection\request\TebexRequest;
use muqsit\tebex\api\connection\response\TebexResponseHandler;

/**
 * A simple TebexConnection-based implementation of TebexApi.
 * Usage:
 * 	$api = new ConnectionBasedTebexApi($connection);
 * 	while($do_processing){
 * 		$api->process();
 * 	}
 * 	$api->disconnect();
 */
final class AutoProcessingConnectionBasedTebexApi extends BaseTebexApi{

	public static function cast(ConnectionBasedTebexApi $api) : self{
		return new self($api);
	}

	private ConnectionBasedTebexApi $inner;

	public function __construct(ConnectionBasedTebexApi $inner){
		$this->inner = $inner;
	}

	public function request(TebexRequest $request, TebexResponseHandler $callback) : void{
		$this->inner->request($request, $callback);
		$this->wait();
	}

	public function getLatency() : float{
		return $this->inner->getLatency();
	}

	public function process() : void{
		// NOOP, requests are instantly processed.
	}

	public function wait() : void{
		$this->inner->wait();
	}

	public function disconnect() : void{
		$this->inner->disconnect();
	}
}
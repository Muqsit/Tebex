<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

use muqsit\tebex\api\TebexResponse;

final class TebexResponseSuccessHolder extends TebexResponseHolder{

	/** @var TebexResponse */
	private $response;

	public function __construct(int $handler_id, float $latency, TebexResponse $response){
		parent::__construct($handler_id, $latency);
		$this->response = $response;
	}

	public function trigger(TebexResponseHandler $handler) : void{
		($handler->on_success)($this->response);
	}
}
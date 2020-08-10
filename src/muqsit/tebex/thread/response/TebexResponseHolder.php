<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

abstract class TebexResponseHolder{

	/** @var int */
	public $handler_id;

	/** @var float */
	public $latency;

	public function __construct(int $handler_id, float $latency){
		$this->handler_id = $handler_id;
		$this->latency = $latency;
	}

	abstract public function trigger(TebexResponseHandler $handler) : void;
}
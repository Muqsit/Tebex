<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue\commands;

abstract class TebexQueuedCommandConditions{

	private int $delay;

	public function __construct(int $delay){
		$this->delay = $delay;
	}

	final public function getDelay() : int{
		return $this->delay;
	}
}
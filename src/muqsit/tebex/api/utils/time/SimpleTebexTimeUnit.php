<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\time;

final class SimpleTebexTimeUnit implements TebexTimeUnit{

	/** @var string */
	private $name;

	/** @var int */
	private $factor;

	public function __construct(string $name, int $factor){
		$this->name = $name;
		$this->factor = $factor;
	}

	public function getName() : string{
		return $this->name;
	}

	public function toSeconds(int $value) : int{
		return $value * $this->factor;
	}
}
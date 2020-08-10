<?php

declare(strict_types=1);

namespace muqsit\tebex\api\sales;

final class TebexSaleDiscountInfo{

	/** @var string */
	private $type;

	/** @var int */
	private $percentage;

	/** @var int */
	private $value;

	public function __construct(string $type, int $percentage, int $value){
		$this->type = $type;
		$this->percentage = $percentage;
		$this->value = $value;
	}

	public function getType() : string{
		return $this->type;
	}

	public function getPercentage() : int{
		return $this->percentage;
	}

	public function getValue() : int{
		return $this->value;
	}
}

<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

final class TebexDiscountInfo{

	public const DISCOUNT_TYPE_PERCENTAGE = "percentage";
	public const DISCOUNT_TYPE_VALUE = "value";

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 */
	public static function fromTebexResponse(array $response) : self{
		/**
		 * @var array{
		 * 		type: string,
		 * 		percentage: int,
		 * 		value: float
		 * } $response
		 */

		return new self(
			$response["type"],
			$response["percentage"],
			$response["value"]
		);
	}

	/** @var string */
	private $type;

	/** @var int */
	private $percentage;

	/** @var float */
	private $value;

	public function __construct(string $type, int $percentage, float $value){
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

	public function getValue() : float{
		return $this->value;
	}
}

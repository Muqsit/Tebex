<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\time;

use InvalidArgumentException;

final class TebexTime{

	public static function create(int $value, string $unit) : self{
		$time_unit = TebexTimeUnitManager::get($unit);
		if($time_unit === null){
			throw new InvalidArgumentException("Invalid time unit: {$unit}");
		}
		return new self($value, $time_unit);
	}

	private int $value;
	private TebexTimeUnit $unit;

	public function __construct(int $value, TebexTimeUnit $unit){
		$this->value = $value;
		$this->unit = $unit;
	}

	public function getValue() : int{
		return $this->value;
	}

	public function getUnit() : TebexTimeUnit{
		return $this->unit;
	}

	public function toSeconds() : int{
		return $this->unit->toSeconds($this->value);
	}
}

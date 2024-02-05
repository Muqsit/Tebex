<?php

declare(strict_types=1);

namespace muqsit\tebex\utils;

use InvalidArgumentException;
use function is_array;
use function is_int;
use function is_string;

final class TypeValidator{

	private static function printValue(mixed $value) : string{
		return var_export($value, true) ?? "";
	}

	public static function validateInt(string $key, mixed $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) : int{
		is_int($value) || throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		if($value < $min || $value > $max){
			throw new InvalidArgumentException("{$key}'s value is out of range [{$min}, {$max}]");
		}
		return $value;
	}

	public static function validateString(string $key, mixed $value) : string{
		is_string($value) || throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		return $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return string[]
	 */
	public static function validateStringList(string $key, mixed $value) : array{
		is_array($value) || throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		foreach($value as $index => $item){
			self::validateString("{$key}.{$index}", $item);
		}
		return $value;
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\utils;

use InvalidArgumentException;

final class TypeValidator{

	/**
	 * @param mixed $value
	 * @return string
	 */
	private static function printValue($value) : string{
		return var_export($value, true) ?? "";
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function validateInt(string $key, $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) : int{
		if(!is_int($value)){
			throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		}
		if($value < $min || $value > $max){
			throw new InvalidArgumentException("{$key}'s value is out of range [{$min}, {$max}]");
		}
		return $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return string
	 */
	public static function validateString(string $key, $value) : string{
		if(!is_string($value)){
			throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		}
		return $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return string[]
	 */
	public static function validateStringList(string $key, $value) : array{
		if(!is_array($value)){
			throw new InvalidArgumentException("Invalid value for {$key}: " . self::printValue($value));
		}
		foreach($value as $index => $item){
			self::validateString("{$key}.{$index}", $item);
		}
		return $value;
	}
}
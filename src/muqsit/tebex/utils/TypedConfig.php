<?php

declare(strict_types=1);

namespace muqsit\tebex\utils;

use pocketmine\utils\Config;

final class TypedConfig{

	public function __construct(
		readonly private Config $config
	){}

	public function getInt(string $key, int $default = 0, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) : int{
		return TypeValidator::validateInt($key, $this->config->get($key, $default), $min, $max);
	}

	public function getNestedInt(string $key, int $default = 0, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) : int{
		return TypeValidator::validateInt($key, $this->config->getNested($key, $default), $min, $max);
	}

	public function getString(string $key) : string{
		return TypeValidator::validateString($key, $this->config->get($key));
	}

	public function getNestedString(string $key) : string{
		return TypeValidator::validateString($key, $this->config->getNested($key));
	}

	/**
	 * @param string $key
	 * @param string[] $default
	 * @return string[]
	 */
	public function getStringList(string $key, array $default = []) : array{
		return TypeValidator::validateStringList($key, $this->config->get($key, $default));
	}

	/**
	 * @param string $key
	 * @param string[] $default
	 * @return string[]
	 */
	public function getNestedStringList(string $key, array $default = []) : array{
		return TypeValidator::validateStringList($key, $this->config->getNested($key, $default));
	}
}
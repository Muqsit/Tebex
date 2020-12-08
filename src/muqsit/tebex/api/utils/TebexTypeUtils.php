<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

use UnexpectedValueException;

final class TebexTypeUtils{

	public static function stringToBoolean(string $string) : bool{
		if($string === "true"){
			return true;
		}

		if($string === "false"){
			return false;
		}

		throw new UnexpectedValueException("{$string} is not a valid boolean string");
	}

	public static function booleanToString(bool $boolean) : string{
		return $boolean ? "true" : "false";
	}
}
<?php


namespace muqsit\tebex\api\utils\time;


final class DurationParser {
	public static function parse(string $argument): int {
		if(!self::isValid($argument)){
			throw new \UnexpectedValueException($argument . " is not a valid duration");
		}
		static $time_units = [
			"y" => "year",
			"M" => "month",
			"w" => "week",
			"d" => "day",
			"h" => "hour",
			"m" => "minute",
			"s" => "second"
		];
		$parts = str_split($argument);
		$time = "";
		$i = -1;
		foreach($parts as $part) {
			$i++;
			if(isset($time_units[$part])) {
				$unit = $time_units[$part];
				$n = implode("", array_slice($parts, 0, $i));
				$time .= "$n $unit ";
				array_splice($parts, 0, $i + 1);
				$i = -1;
			}
		}
		// todo: throw more errors?
		return strtotime(trim($time));
	}

	public static function isValid(string $duration):bool {
		return preg_match("/^(?:\d+[yMwdhms])+$/", $duration);
	}
}
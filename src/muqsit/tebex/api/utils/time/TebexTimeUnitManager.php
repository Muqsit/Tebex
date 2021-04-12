<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\time;

final class TebexTimeUnitManager{

	private static bool $init = false;

	/** @var TebexTimeUnit[] */
	private static array $known_units = [];

	public static function init() : void{
		self::$init = true;
		self::register(new NoneTebexTimeUnit());
		self::register(new SimpleTebexTimeUnit("Minute", 60));
		self::register(new SimpleTebexTimeUnit("Hour", 60 * 60));
		self::register(new SimpleTebexTimeUnit("Day", 60 * 60 * 24));
		self::register(new SimpleTebexTimeUnit("Week", 60 * 60 * 24 * 7));
		self::register(new SimpleTebexTimeUnit("Month", 60 * 60 * 24 * 7 * 30));
		self::register(new SimpleTebexTimeUnit("Year", 60 * 60 * 24 * 7 * 365));
	}

	public static function register(TebexTimeUnit $unit) : void{
		self::$known_units[strtolower($unit->getName())] = $unit;
	}

	public static function get(string $name) : ?TebexTimeUnit{
		if(!self::$init){
			self::init();
		}
		return self::$known_units[strtolower($name)] ?? null;
	}
}
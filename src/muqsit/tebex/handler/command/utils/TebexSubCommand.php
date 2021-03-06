<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command\utils;

use pocketmine\command\CommandExecutor;

final class TebexSubCommand{

	/** @var string */
	public $name;

	/** @var string */
	public $description;

	/** @var string[] */
	public $aliases;

	/** @var CommandExecutor */
	public $executor;

	/**
	 * @param string $name
	 * @param string $description
	 * @param CommandExecutor $executor
	 * @param string[] $aliases
	 */
	public function __construct(string $name, string $description, CommandExecutor $executor, array $aliases = []){
		$this->name = $name;
		$this->description = $description;
		$this->aliases = $aliases;
		$this->executor = $executor;
	}
}
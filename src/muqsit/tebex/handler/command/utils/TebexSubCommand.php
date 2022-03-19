<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command\utils;

use pocketmine\command\CommandExecutor;

final class TebexSubCommand{

	/**
	 * @param string $name
	 * @param string $description
	 * @param CommandExecutor $executor
	 * @param string[] $aliases
	 */
	public function __construct(
		public string $name,
		public string $description,
		public CommandExecutor $executor,
		public array $aliases = []
	){}
}
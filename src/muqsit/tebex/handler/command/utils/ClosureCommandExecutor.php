<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command\utils;

use Closure;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

final class ClosureCommandExecutor implements CommandExecutor{

	/**
	 * @param Closure(CommandSender, Command, string, string[]) : bool $executor
	 */
	public function __construct(
		readonly private Closure $executor
	){}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		return ($this->executor)($sender, $command, $label, $args);
	}
}
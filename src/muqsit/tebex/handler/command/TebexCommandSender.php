<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;

final class TebexCommandSender extends ConsoleCommandSender{

	public static function instance() : self{
		static $instance = null;
		return $instance ?? $instance = new self(Server::getInstance(), Server::getInstance()->getLanguage());
	}

	public function getName() : string{
		return "TEBEX";
	}
}
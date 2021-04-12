<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use InvalidStateException;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\permission\DefaultPermissions;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class TebexCommandSender extends ConsoleCommandSender{

	private static ?TebexCommandSender $instance = null;

	public static function hasInstance() : bool{
		return self::$instance !== null;
	}

	public static function getInstance() : TebexCommandSender{
		if(self::$instance === null){
			throw new InvalidStateException("No instance of " . self::class . " has been set");
		}
		return self::$instance;
	}

	public static function setInstance(TebexCommandSender $instance) : void{
		self::$instance = $instance;
	}

	public function __construct(Plugin $plugin, Language $language){
		parent::__construct(Server::getInstance(), $language);
		$this->addAttachment($plugin, DefaultPermissions::ROOT_OPERATOR, true);
	}

	public function getName() : string{
		return "TEBEX";
	}
}
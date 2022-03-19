<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use muqsit\tebexapi\utils\TebexException;
use muqsit\tebex\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

final class UnregisteredTebexCommandExecutor implements CommandExecutor{

	public function __construct(
		private Loader $plugin
	){}

	public static function handleTypeSecret(Loader $loader, CommandSender $sender, string $secret) : void{
		$info = null;

		try{
			$info = $loader->setSecret($secret);
		}catch(TebexException $e){
			$sender->sendMessage($e->getMessage());
		}

		if($info !== null){
			$config = $loader->getConfig();
			$config->set("secret", $secret);
			$config->save();

			$account = $info->getAccount();
			$server = $info->getServer();
			$sender->sendMessage("Successfully logged in to server (#{$server->getId()}) {$server->getName()} as (#{$account->getId()}) {$account->getName()}!");
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[0], $args[1]) && $args[0] === "secret"){
			self::handleTypeSecret($this->plugin, $sender, $args[1]);
			return true;
		}

		$sender->sendMessage("Usage: /{$label} secret <secret>");
		return false;
	}
}
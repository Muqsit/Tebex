<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\Loader;
use Ds\Set;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

final class TebexCommandExecutor extends UnregisteredTebexCommandExecutor{

	/** @var TebexHandler */
	private $handler;

	/** @var Set<string> */
	private $disabled;

	/**
	 * @param Loader $plugin
	 * @param TebexHandler $handler
	 * @param string[] $disabled
	 */
	public function __construct(Loader $plugin, TebexHandler $handler, array $disabled){
		parent::__construct($plugin);
		$this->handler = $handler;
		$this->disabled = new Set(array_map("strtolower", $disabled));
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[0])){
			if($this->disabled->contains($args[0])){
				$sender->sendMessage(TextFormat::RED . "This command has been disabled.");
				return true;
			}

			switch($args[0]){
				case "info":
					$info = $this->plugin->getInformation();
					$account = $info->getAccount();
					$server = $info->getServer();

					$sender->sendMessage(
						"" . TextFormat::EOL .
						TextFormat::BOLD . TextFormat::WHITE . "Tebex Account" . TextFormat::RESET . TextFormat::EOL .
						TextFormat::WHITE . "ID: " . TextFormat::GRAY . $account->getId() . TextFormat::EOL .
						TextFormat::WHITE . "Domain: " . TextFormat::GRAY . $account->getDomain() . TextFormat::EOL .
						TextFormat::WHITE . "Name: " . TextFormat::GRAY . $account->getName() . TextFormat::EOL .
						TextFormat::WHITE . "Currency: " . TextFormat::GRAY . "{$account->getCurrency()->getIso4217()} ({$account->getCurrency()->getSymbol()})" . TextFormat::EOL .
						TextFormat::WHITE . "Online Mode: " . TextFormat::GRAY . ($account->isOnlineModeEnabled() ? "Enabled" : "Disabled") . TextFormat::EOL .
						TextFormat::WHITE . "Game Type: " . TextFormat::GRAY . $account->getGameType() . TextFormat::EOL .
						TextFormat::WHITE . "Event Logging: " . TextFormat::GRAY . ($account->isLogEventsEnabled() ? "Enabled" : "Disabled") . TextFormat::EOL .
						"" . TextFormat::EOL .
						TextFormat::BOLD . TextFormat::WHITE . "Tebex Server" . TextFormat::RESET . TextFormat::EOL .
						TextFormat::WHITE . "ID: " . TextFormat::GRAY . $server->getId() . TextFormat::EOL .
						TextFormat::WHITE . "Name: " . TextFormat::GRAY . $server->getName() . TextFormat::EOL .
						"" . TextFormat::EOL .
						TextFormat::BOLD . TextFormat::WHITE . "Tebex API" . TextFormat::RESET . TextFormat::EOL .
						TextFormat::WHITE . "Latency: " . TextFormat::GRAY . round($this->plugin->getApi()->getLatency() * 1000) . "ms" . TextFormat::EOL .
						"" . TextFormat::EOL
					);
					return true;
				case "forcecheck":
				case "refresh":
					static $command_senders_force_check = null;
					if($command_senders_force_check === null){
						/** @var CommandSender[] $command_senders_force_check */
						$command_senders_force_check = [];
						$this->handler->getDueCommandsHandler()->refresh(static function(int $offline_commands, int $online_players) use(&$command_senders_force_check) : void{
							if($command_senders_force_check !== null){
								foreach($command_senders_force_check as $sender){
									if(!($sender instanceof Player) || $sender->isOnline()){
										$sender->sendMessage(
											TextFormat::WHITE . "Refreshed command queue" . TextFormat::EOL .
											TextFormat::WHITE . "Offline commands fetched: " . TextFormat::GRAY . $offline_commands . TextFormat::EOL .
											TextFormat::WHITE . "Online players due: " . TextFormat::GRAY . $online_players
										);
									}
								}
								$command_senders_force_check = null;
							}
						});
					}

					$command_senders_force_check[spl_object_id($sender)] = $sender;
					$sender->sendMessage(TextFormat::GRAY . "Refreshing command queue...");
					return true;
				case "dropall":
					static $command_senders_dropall = null;
					if($command_senders_dropall === null){
						/** @var CommandSender[] $command_senders_dropall */
						$command_senders_dropall = [];
						$this->handler->getDueCommandsHandler()->markAllAsExecuted(static function(int $marked) use(&$command_senders_dropall) : void{
							if($command_senders_dropall !== null){
								foreach($command_senders_dropall as $sender){
									if(!($sender instanceof Player) || $sender->isOnline()){
										$sender->sendMessage(TextFormat::WHITE . "Marked " . TextFormat::GRAY . $marked . TextFormat::WHITE . " command(s) as executed.");
									}
								}
								$command_senders_dropall = null;
							}
						});
					}

					$command_senders_dropall[spl_object_id($sender)] = $sender;
					$sender->sendMessage(TextFormat::GRAY . "Dropping all queued commands");
					return true;
				case "secret":
					if(isset($args[1])){
						$this->onTypeSecret($sender, $command, $label, $args[1]);
					}else{
						$sender->sendMessage("Usage: /{$label} {$args[0]} <secret>");
					}
					return true;
			}
		}

		$sender->sendMessage(
			TextFormat::BOLD . TextFormat::WHITE . "Tebex Commands" . TextFormat::RESET . TextFormat::EOL .
			TextFormat::WHITE . "/{$label} secret" . TextFormat::GRAY . " - Set Tebex server secret" . TextFormat::EOL .
			TextFormat::WHITE . "/{$label} info" . TextFormat::GRAY . " - Fetch Tebex account, server and API info" . TextFormat::EOL .
			TextFormat::WHITE . "/{$label} refresh" . TextFormat::GRAY . " - Refresh offline and online command queues" . TextFormat::EOL .
			TextFormat::WHITE . "/{$label} dropall" . TextFormat::GRAY . " - Drop all queued commands" . TextFormat::EOL
		);
		return false;
	}
}
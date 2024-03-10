<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use InvalidArgumentException;
use muqsit\tebex\handler\command\utils\ClosureCommandExecutor;
use muqsit\tebex\handler\command\utils\TebexSubCommand;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\Loader;
use muqsit\tebexapi\utils\TebexException;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class RegisteredTebexCommandExecutor implements CommandExecutor{

	/** @var array<string, TebexSubCommand> */
	private array $sub_commands = [];

	/** @var array<string, string> */
	private array $aliases = [];

	public function __construct(
		readonly private Loader $plugin,
		readonly private TebexHandler $handler
	){
		$this->registerDefaultSubCommands();
	}

	private function registerDefaultSubCommands() : void{
		$this->registerSubCommand(new TebexSubCommand("secret", "Set Tebex server secret", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				if(isset($args[1])){
					UnregisteredTebexCommandExecutor::handleTypeSecret($this->plugin, $sender, $args[1]);
				}else{
					$sender->sendMessage("Usage: /{$label} {$args[0]} <secret>");
				}
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("info", "Fetch Tebex account, server and API info", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				$info = $this->plugin->getInformation();
				$account = $info->account;
				$server = $info->server;

				$sender->sendMessage(
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex Account" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "ID: " . TextFormat::GRAY . $account->id . TextFormat::EOL .
					TextFormat::WHITE . "Domain: " . TextFormat::GRAY . $account->domain . TextFormat::EOL .
					TextFormat::WHITE . "Name: " . TextFormat::GRAY . $account->name . TextFormat::EOL .
					TextFormat::WHITE . "Currency: " . TextFormat::GRAY . "{$account->currency->iso_4217} ({$account->currency->symbol})" . TextFormat::EOL .
					TextFormat::WHITE . "Online Mode: " . TextFormat::GRAY . ($account->online_mode ? "Enabled" : "Disabled") . TextFormat::EOL .
					TextFormat::WHITE . "Game Type: " . TextFormat::GRAY . $account->game_type . TextFormat::EOL .
					TextFormat::WHITE . "Event Logging: " . TextFormat::GRAY . ($account->log_events ? "Enabled" : "Disabled") . TextFormat::EOL .
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex Server" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "ID: " . TextFormat::GRAY . $server->id . TextFormat::EOL .
					TextFormat::WHITE . "Name: " . TextFormat::GRAY . $server->name . TextFormat::EOL .
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex API" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "Latency: " . TextFormat::GRAY . round($this->plugin->getApi()->getLatency() * 1000) . "ms" . TextFormat::EOL .
					"" . TextFormat::EOL
				);
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("refresh", "Refresh offline and online command queues", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				/** @var array<int, CommandSender>|null $command_senders_force_check */
				static $command_senders_force_check = null;
				if($command_senders_force_check === null){
					$command_senders_force_check = [];
					$this->handler->getDueCommandsHandler()->refresh(static function(int|TebexException $offline_commands, int|TebexException $online_players) use(&$command_senders_force_check) : void{
						if($command_senders_force_check !== null){
							foreach($command_senders_force_check as $sender){
								if(!($sender instanceof Player) || $sender->isOnline()){
									$sender->sendMessage(
										TextFormat::WHITE . "Refreshed command queue" . TextFormat::EOL .
										TextFormat::WHITE . "Offline commands fetched: " . TextFormat::GRAY . ($offline_commands instanceof TebexException ? TextFormat::RED . TextFormat::ITALIC . $offline_commands->getMessage() . TextFormat::RESET : $offline_commands) . TextFormat::EOL .
										TextFormat::WHITE . "Online players due: " . TextFormat::GRAY . ($online_players instanceof TebexException ? TextFormat::RED . TextFormat::ITALIC . $online_players->getMessage() . TextFormat::RESET : $online_players)
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
			}
		), ["forcecheck"]));

		$this->registerSubCommand(new TebexSubCommand("dropall", "Drop all queued commands", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				/** @var array<int, CommandSender>|null $command_senders_dropall */
				static $command_senders_dropall = null;
				if($command_senders_dropall === null){
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
			}
		)));
	}

	public function registerSubCommand(TebexSubCommand $sub_command) : void{
		$this->sub_commands[$sub_command->name] = $sub_command;
		foreach($sub_command->aliases as $alias){
			$this->aliases[$alias] = $sub_command->name;
		}
	}

	public function unregisterSubCommand(string $name) : void{
		isset($this->sub_commands[$name]) || throw new InvalidArgumentException("Tried unregistering an unregistered sub-command: {$name}");
		foreach($this->sub_commands[$name]->aliases as $alias){
			unset($this->aliases[$alias]);
		}
		unset($this->sub_commands[$name]);
	}

	public function getSubCommand(string $name) : ?TebexSubCommand{
		return $this->sub_commands[$name] ?? (isset($this->aliases[$name]) ? $this->sub_commands[$this->aliases[$name]] : null);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[0])){
			$sub_command = $this->getSubCommand($args[0]);
			if($sub_command !== null){
				return $sub_command->executor->onCommand($sender, $command, $label, $args);
			}
		}

		$help = TextFormat::BOLD . TextFormat::WHITE . "Tebex Commands" . TextFormat::RESET . TextFormat::EOL;
		foreach($this->sub_commands as $sub_command){
			$help .= TextFormat::WHITE . "/{$label} {$sub_command->name}" . TextFormat::GRAY . " - {$sub_command->description}" . TextFormat::EOL;
		}
		$sender->sendMessage(rtrim($help, TextFormat::EOL));
		return false;
	}
}
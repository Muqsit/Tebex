<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due;

use Closure;
use InvalidArgumentException;
use Logger;
use muqsit\tebex\handler\due\playerlist\indexer\NameBasedPlayerIndexer;
use muqsit\tebex\handler\due\playerlist\indexer\XuidBasedPlayerIndexer;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\endpoint\queue\commands\online\TebexQueuedOnlineCommandsInfo;
use muqsit\tebexapi\endpoint\queue\TebexDuePlayersInfo;
use muqsit\tebex\handler\due\playerlist\TebexDuePlayerHolder;
use muqsit\tebex\handler\due\playerlist\TebexDuePlayerList;
use muqsit\tebex\handler\due\playerlist\TebexDuePlayerListListener;
use muqsit\tebex\handler\due\session\TebexPlayerSession;
use muqsit\tebex\handler\TebexApiUtils;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\Loader;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

final class TebexDueCommandsHandler{

	/**
	 * @param string $game_type
	 * @param Closure(Player, TebexDuePlayerHolder) : void $on_match
	 * @return TebexDuePlayerList
	 */
	private static function getListFromGameType(string $game_type, Closure $on_match) : TebexDuePlayerList{
		return new TebexDuePlayerList(match($game_type){
			"Minecraft (Bedrock)" => new XuidBasedPlayerIndexer(),
			"Minecraft Offline" => new NameBasedPlayerIndexer(),
			default => throw new InvalidArgumentException("Unsupported game server type {$game_type}")
		}, $on_match);
	}

	readonly private TebexDueOfflineCommandsHandler $offline_commands_handler;
	readonly private TebexDuePlayerList $list;
	readonly private Logger $logger;
	private bool $is_idle = true;

	public function __construct(
		readonly private Loader $plugin,
		readonly private TebexHandler $handler
	){
		TebexPlayerSession::init($plugin);

		$this->logger = $plugin->getLogger();
		$this->offline_commands_handler = new TebexDueOfflineCommandsHandler($plugin, $handler);

		$api = $plugin->getApi();
		$this->list = self::getListFromGameType($plugin->getInformation()->account->game_type, function(Player $player, TebexDuePlayerHolder $holder) use($api, $handler) : void{
			$session = $this->list->getOnlinePlayer($player);
			assert($session !== null);
			$api->getQueuedOnlineCommands($holder->player->id, TebexResponseHandler::onSuccess(function(TebexQueuedOnlineCommandsInfo $info) use($player, $session, $holder, $handler) : void{
				if(!$player->isOnline()){
					return;
				}

				$commands = $info->commands;
				$total_commands = count($commands);
				$timestamp = microtime(true);
				foreach($commands as $tebex_command){
					$session->executeOnlineCommand($tebex_command, $holder->player, function(bool $success) use($tebex_command, $handler, &$total_commands, $player, $holder, $timestamp) : void{
						$command_string = TebexApiUtils::onlineFormatCommand($tebex_command->command, $player, $holder->player);
						if(!$success){
							$this->logger->warning("Failed to execute online command: {$command_string}");
							return;
						}

						$command_id = $tebex_command->id;
						$handler->queueCommandDeletion($command_id);
						if(--$total_commands === 0){
							$current_holder = $this->list->getTebexAwaitingPlayer($player);
							if($current_holder !== null && $current_holder->created < $timestamp){
								$this->list->remove($current_holder);
							}
						}
						$this->logger->info("Executed online command #{$command_id}: {$command_string}");
					});
				}
			}));
		});

		$plugin->getServer()->getPluginManager()->registerEvents(new TebexDuePlayerListListener($this->list), $plugin);
		$plugin->getServer()->getPluginManager()->registerEvents(new TebexLazyDueCommandsListener($this), $plugin);
	}

	/**
	 * @param (Closure(int) : void)|null $callback
	 */
	public function markAllAsExecuted(?Closure $callback = null) : void{
		$this->plugin->getApi()->getDuePlayersList(TebexResponseHandler::onSuccess(function(TebexDuePlayersInfo $result) use($callback) : void{
			$marked = 0;
			$batches = count($result->players) + 1;

			$cb = static function(int $done) use(&$marked, &$batches, $callback) : void{
				$marked += $done;
				if(--$batches === 0){
					if($callback !== null){
						$callback($marked);
					}
				}
			};

			$this->offline_commands_handler->markAllAsExecuted($cb);

			foreach($result->players as $player){
				$this->plugin->getApi()->getQueuedOnlineCommands($player->id, TebexResponseHandler::onSuccess(function(TebexQueuedOnlineCommandsInfo $info) use($cb) : void{
					$commands = $info->commands;
					foreach($commands as $command){
						$this->handler->queueCommandDeletion($command->id);
					}
					$cb(count($commands));
				}));
			}
		}));
	}

	private function scheduleDuePlayersCheck() : bool{
		if(!$this->is_idle){
			return false;
		}

		$this->is_idle = false;
		$server = $this->plugin->getServer();
		$this->checkDuePlayers(function() use($server) : bool{
			if(count($server->getOnlinePlayers()) === 0){
				$this->is_idle = true;
				$this->logger->debug("Online commands handler is now idle");
				return false;
			}
			return true;
		});
		return true;
	}

	public function getList() : TebexDuePlayerList{
		return $this->list;
	}

	/**
	 * @param (Closure(int, int) : void)|null $callback
	 */
	public function refresh(?Closure $callback = null) : void{
		$this->offline_commands_handler->check(function(int $offline_cmds_count) use($callback) : void{
			$this->checkDuePlayers(null, static function(int $due_players_count) use($offline_cmds_count, $callback) : void{
				if($callback !== null){
					$callback($offline_cmds_count, $due_players_count);
				}
			});
		});
	}

	/**
	 * @param (Closure() : bool)|null $reschedule_condition
	 * @param (Closure(int) : void)|null $callback
	 */
	public function checkDuePlayers(?Closure $reschedule_condition = null, ?Closure $callback = null) : void{
		$this->plugin->getApi()->getDuePlayersList(TebexResponseHandler::onSuccess(function(TebexDuePlayersInfo $result) use($reschedule_condition, $callback) : void{
			$this->onFetchDuePlayers($result);
			if($callback !== null){
				$callback(count($result->players));
			}
			if($reschedule_condition !== null && $reschedule_condition()){
				$next_check = $result->meta->next_check;
				$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($reschedule_condition, $callback) : void{ $this->checkDuePlayers($reschedule_condition, $callback); }), $next_check * 20);
			}
		}));
	}

	private function onFetchDuePlayers(TebexDuePlayersInfo $result) : void{
		$players = $result->players;
		$this->list->update($players);

		$players_c = count($players);
		$this->logger->debug("{$players_c} player" . ($players_c === 1 ? " is " : "s are") . " in the online commands queue");
	}

	public function onPlayerJoin() : void{
		if($this->scheduleDuePlayersCheck()){
			$this->logger->debug("Online commands handler is no longer idle");
		}
	}
}
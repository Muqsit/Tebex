<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due;

use Closure;
use Logger;
use muqsit\tebex\api\connection\response\TebexResponseHandler;
use muqsit\tebex\api\endpoint\queue\commands\offline\TebexQueuedOfflineCommand;
use muqsit\tebex\api\endpoint\queue\commands\offline\TebexQueuedOfflineCommandsInfo;
use muqsit\tebex\handler\command\TebexCommandSender;
use muqsit\tebex\handler\TebexApiUtils;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\Loader;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

final class TebexDueOfflineCommandsHandler{

	private Loader $plugin;
	private Logger $logger;
	private TebexHandler $handler;

	/** @var int[] */
	private array $delayed = [];

	public function __construct(Loader $plugin, TebexHandler $handler, int $check_period = 60 * 20){
		$this->plugin = $plugin;
		$this->logger = $plugin->getLogger();
		$this->handler = $handler;
		$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{ $this->check(); }), $check_period);
	}

	/**
	 * @param Closure|null $callback
	 *
	 * @phpstan-param Closure(int) : void $callback
	 */
	public function check(?Closure $callback = null) : void{
		$this->plugin->getApi()->getQueuedOfflineCommands(TebexResponseHandler::onSuccess(function(TebexQueuedOfflineCommandsInfo $info) use($callback) : void{
			if($callback !== null){
				$callback(count($info->getCommands()));
			}
			$this->onFetchDueOfflineCommands($info);
		}));
	}

	/**
	 * @param Closure|null $callback
	 *
	 * @phpstan-param Closure(int) : void $callback
	 */
	public function markAllAsExecuted(?Closure $callback = null) : void{
		$this->plugin->getApi()->getQueuedOfflineCommands(TebexResponseHandler::onSuccess(function(TebexQueuedOfflineCommandsInfo $info) use($callback) : void{
			$commands = $info->getCommands();
			foreach($commands as $command){
				$this->handler->queueCommandDeletion($command->getId());
			}
			if($callback !== null){
				$callback(count($commands));
			}
		}));
	}

	private function onFetchDueOfflineCommands(TebexQueuedOfflineCommandsInfo $info) : void{
		$commands = $info->getCommands();

		$commands_c = count($commands);
		$this->logger->debug("Fetched {$commands_c} offline command" . ($commands_c === 1 ? "" : "s"));

		foreach($commands as $command){
			$this->executeCommand($command, function(bool $success) use($command) : void{
				$command_string = TebexApiUtils::offlineFormatCommand($command->getCommand(), $command->getPlayer());
				if($success){
					$command_id = $command->getId();
					$this->handler->queueCommandDeletion($command_id);
					$this->logger->info("Executed offline command #{$command_id}: {$command_string}");
				}else{
					$this->logger->warning("Failed to execute offline command: {$command_string}");
				}
			});
		}
	}

	/**
	 * @param TebexQueuedOfflineCommand $command
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(bool) : void $callback
	 */
	private function executeCommand(TebexQueuedOfflineCommand $command, Closure $callback) : void{
		$delay = $command->getConditions()->getDelay();
		if($delay > 0){
			if(!isset($this->delayed[$id = $command->getId()])){
				$this->delayed[$id] = $id;
				$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($command, $callback) : void{
					$callback($this->instantlyExecuteCommand($command));
				}), $delay * 20);
			}
		}else{
			$callback($this->instantlyExecuteCommand($command));
		}
	}

	private function instantlyExecuteCommand(TebexQueuedOfflineCommand $command) : bool{
		return Server::getInstance()->dispatchCommand(TebexCommandSender::getInstance(), TebexApiUtils::offlineFormatCommand($command->getCommand(), $command->getPlayer()));
	}
}
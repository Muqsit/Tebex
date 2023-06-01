<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\session;

use Closure;
use muqsit\tebexapi\endpoint\queue\commands\online\TebexQueuedOnlineCommand;
use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use muqsit\tebex\handler\command\TebexCommandSender;
use muqsit\tebex\handler\TebexApiUtils;
use muqsit\tebex\Loader;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

final class TebexPlayerSession{

	private static TaskScheduler $scheduler;

	public static function init(Loader $plugin) : void{
		self::$scheduler = $plugin->getScheduler();
	}

	/** @var DelayedOnlineCommandHandler[] */
	private array $delayed_online_command_handlers = [];

	public function __construct(
		readonly private Player $player
	){}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function destroy() : void{
		foreach($this->delayed_online_command_handlers as $handler){
			$handler->handler->cancel();
		}
		$this->delayed_online_command_handlers = [];
	}

	/**
	 * @param TebexQueuedOnlineCommand $command
	 * @param TebexDuePlayer $due_player
	 * @param Closure(bool) : void $callback
	 */
	public function executeOnlineCommand(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player, Closure $callback) : void{
		$conditions = $command->conditions;
		$delay = $conditions->delay;
		if($delay > 0){
			$this->scheduleCommandForDelay($command, $due_player, $delay * 20, $callback);
		}else{
			$callback($this->instantlyExecuteOnlineCommand($command, $due_player));
		}
	}

	/**
	 * @param TebexQueuedOnlineCommand $command
	 * @param TebexDuePlayer $due_player
	 * @param int $delay
	 * @param Closure(bool) : void $callback
	 * @return bool
	 */
	private function scheduleCommandForDelay(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player, int $delay, Closure $callback) : bool{
		if(isset($this->delayed_online_command_handlers[$id = $command->id])){
			return false;
		}

		$this->delayed_online_command_handlers[$id] = new DelayedOnlineCommandHandler($command, self::$scheduler->scheduleDelayedTask(new ClosureTask(function() use($id, $command, $due_player, $callback) : void{
			$callback($this->instantlyExecuteOnlineCommand($command, $due_player));
			unset($this->delayed_online_command_handlers[$id]);
		}), $delay));
		return true;
	}

	private function instantlyExecuteOnlineCommand(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player) : bool{
		$conditions = $command->conditions;
		$slots = $conditions->slots;
		if($slots > 0){
			$inventory = $this->player->getInventory();
			$free_slots = $inventory->getSize() - count($inventory->getContents());
			if($free_slots < $slots){
				return false;
			}
		}

		return $this->player->getServer()->dispatchCommand(TebexCommandSender::getInstance(), TebexApiUtils::onlineFormatCommand($command->command, $this->player, $due_player));
	}
}
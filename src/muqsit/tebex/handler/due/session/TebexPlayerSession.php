<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\session;

use muqsit\tebex\api\queue\TebexDuePlayer;
use muqsit\tebex\api\queue\commands\online\TebexQueuedOnlineCommand;
use muqsit\tebex\handler\command\TebexCommandSender;
use muqsit\tebex\Loader;
use Closure;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

final class TebexPlayerSession{

	/** @var TaskScheduler */
	private static $scheduler;

	public static function init(Loader $plugin) : void{
		self::$scheduler = $plugin->getScheduler();
	}

	/** @var Player */
	private $player;

	/** @var DelayedOnlineCommandHandler[] */
	private $delayed_online_command_handlers = [];

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function destroy() : void{
		foreach($this->delayed_online_command_handlers as $handler){
			$handler->getHandler()->cancel();
		}
		$this->delayed_online_command_handlers = [];
	}

	/**
	 * @param TebexQueuedOnlineCommand $command
	 * @param TebexDuePlayer $due_player
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(bool) : void $callback
	 */
	public function executeOnlineCommand(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player, Closure $callback) : void{
		$conditions = $command->getConditions();
		$delay = $conditions->getDelay();
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
	 * @param Closure $callback
	 * @return bool
	 *
	 * @phpstan-param Closure(bool) : void $callback
	 */
	private function scheduleCommandForDelay(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player, int $delay, Closure $callback) : bool{
		if(!isset($this->delayed_online_command_handlers[$id = $command->getId()])){
			$this->delayed_online_command_handlers[$id] = new DelayedOnlineCommandHandler($command, self::$scheduler->scheduleDelayedTask(new ClosureTask(function() use($command, $due_player, $callback) : void{
				$callback($this->instantlyExecuteOnlineCommand($command, $due_player));
			}), $delay));
			return true;
		}
		return false;
	}

	private function instantlyExecuteOnlineCommand(TebexQueuedOnlineCommand $command, TebexDuePlayer $due_player) : bool{
		$conditions = $command->getConditions();
		$slots = $conditions->getInventorySlots();
		if($slots > 0){
			$inventory = $this->player->getInventory();
			$free_slots = $inventory->getSize() - count($inventory->getContents());
			if($free_slots < $slots){
				return false;
			}
		}

		return $this->player->getServer()->dispatchCommand(TebexCommandSender::instance(), $command->getCommand()->asOnlineFormattedString($this->player, $due_player));
	}
}
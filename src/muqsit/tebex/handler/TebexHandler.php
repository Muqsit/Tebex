<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use muqsit\tebex\api\EmptyTebexResponse;
use muqsit\tebex\Loader;
use muqsit\tebex\handler\due\TebexDueCommandsHandler;
use muqsit\tebex\thread\response\TebexResponseHandler;
use pocketmine\scheduler\ClosureTask;

final class TebexHandler{

	/** @var Loader */
	private $plugin;

	/** @var TebexDueCommandsHandler */
	private $due_commands_handler;

	/** @var int[]|null */
	private $command_ids;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
		$this->init();
	}

	private function init() : void{
		$this->due_commands_handler = new TebexDueCommandsHandler($this->plugin, $this);
	}

	public function getDueCommandsHandler() : TebexDueCommandsHandler{
		return $this->due_commands_handler;
	}

	public function queueCommandDeletion(int $command_id, int ...$command_ids) : void{
		if($this->command_ids === null){
			$this->command_ids = [];
			$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() : void{ $this->deletePendingCommands(); }), 1);
		}

		array_push($this->command_ids, $command_id, ...$command_ids);
	}

	private function deletePendingCommands() : void{
		if($this->command_ids !== null){
			$this->plugin->getApi()->deleteCommands($this->command_ids, TebexResponseHandler::onSuccess(function(EmptyTebexResponse $_) : void{
				if($this->command_ids !== null){
					$commands_c = count($this->command_ids);
					$this->plugin->getLogger()->debug("Deleted {$commands_c} command" . ($commands_c > 1 ? "s" : "") . ": [" . implode(", ", $this->command_ids) . "]");
					$this->command_ids = null;
				}
			}));
		}
	}

	public function shutdown() : void{
		$this->deletePendingCommands();
	}
}
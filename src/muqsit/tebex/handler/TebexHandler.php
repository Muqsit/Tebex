<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use muqsit\tebexapi\connection\response\EmptyTebexResponse;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\utils\TebexException;
use muqsit\tebex\handler\due\TebexDueCommandsHandler;
use muqsit\tebex\Loader;
use pocketmine\scheduler\ClosureTask;

final class TebexHandler{

	private TebexDueCommandsHandler $due_commands_handler;

	/** @var list<int>|null */
	private ?array $command_ids = null;

	private int $pending_commands_batch_counter = 0;

	public function __construct(
		readonly private Loader $plugin
	){
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
			$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask($this->deletePendingCommands(...)), 1);
		}

		array_push($this->command_ids, $command_id, ...$command_ids);
	}

	private function deletePendingCommands() : void{
		if($this->command_ids !== null){
			$command_ids = $this->command_ids;
			$this->command_ids = null;
			$batch_id = ++$this->pending_commands_batch_counter;
			$this->plugin->getLogger()->info("Executing pending command deletion batch #{$batch_id} consisting of: (" . count($command_ids) . ") [" . implode(", ", $command_ids) . "]");
			$this->plugin->getApi()->deleteCommands($command_ids, new TebexResponseHandler(function(EmptyTebexResponse $_) use($batch_id) : void{
				$this->plugin->getLogger()->info("Successfully executed pending command deletion batch #{$batch_id}");
			}, function(TebexException $e) use($batch_id, $command_ids) : void{
				$this->plugin->getLogger()->info("Failed to execute pending command deletion batch #{$batch_id} due to: {$e->getMessage()}, queueing into next batch");
				$this->queueCommandDeletion(...$command_ids);
			}));
		}
	}

	public function shutdown() : void{
		$this->deletePendingCommands();
	}
}
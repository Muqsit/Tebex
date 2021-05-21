<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\session;

use muqsit\tebexapi\endpoint\queue\commands\online\TebexQueuedOnlineCommand;
use pocketmine\scheduler\TaskHandler;

final class DelayedOnlineCommandHandler{

	private TebexQueuedOnlineCommand $command;
	private TaskHandler $handler;

	public function __construct(TebexQueuedOnlineCommand $command, TaskHandler $handler){
		$this->command = $command;
		$this->handler = $handler;
	}

	public function getCommand() : TebexQueuedOnlineCommand{
		return $this->command;
	}

	public function getHandler() : TaskHandler{
		return $this->handler;
	}
}
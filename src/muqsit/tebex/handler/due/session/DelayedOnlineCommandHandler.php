<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\session;

use muqsit\tebex\api\queue\commands\online\TebexQueuedOnlineCommand;
use pocketmine\scheduler\TaskHandler;

final class DelayedOnlineCommandHandler{

	/** @var TebexQueuedOnlineCommand */
	private $command;

	/** @var TaskHandler */
	private $handler;

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
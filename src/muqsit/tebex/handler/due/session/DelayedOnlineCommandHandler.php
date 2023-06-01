<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\session;

use muqsit\tebexapi\endpoint\queue\commands\online\TebexQueuedOnlineCommand;
use pocketmine\scheduler\TaskHandler;

final class DelayedOnlineCommandHandler{

	public function __construct(
		readonly public TebexQueuedOnlineCommand $command,
		readonly public TaskHandler $handler
	){}
}
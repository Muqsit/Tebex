<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue\commands\online;

use muqsit\tebex\api\TebexResponse;

final class TebexQueuedOnlineCommandsInfo implements TebexResponse{

	/** @var TebexQueuedOnlineCommand[] */
	private array $commands;

	/**
	 * @param TebexQueuedOnlineCommand[] $commands
	 */
	public function __construct(array $commands){
		$this->commands = $commands;
	}

	/**
	 * @return TebexQueuedOnlineCommand[]
	 */
	public function getCommands() : array{
		return $this->commands;
	}
}
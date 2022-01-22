<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue\commands\online;

use muqsit\tebex\api\queue\commands\TebexQueuedCommand;

final class TebexQueuedOnlineCommand extends TebexQueuedCommand{

	/** @var TebexQueuedOnlineCommandConditions */
	private $conditions;

	public function __construct(int $id, string $command, ?int $payment_id, ?int $package_id, TebexQueuedOnlineCommandConditions $conditions){
		parent::__construct($id, $command, $payment_id, $package_id);
		$this->conditions = $conditions;
	}

	public function getConditions() : TebexQueuedOnlineCommandConditions{
		return $this->conditions;
	}
}

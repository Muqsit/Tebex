<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue\commands\offline;

use muqsit\tebex\api\endpoint\queue\commands\TebexQueuedCommand;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayer;

final class TebexQueuedOfflineCommand extends TebexQueuedCommand{

	private TebexQueuedOfflineCommandConditions $conditions;
	private TebexDuePlayer $player;

	public function __construct(int $id, string $command, int $payment_id, int $package_id, TebexQueuedOfflineCommandConditions $conditions, TebexDuePlayer $player){
		parent::__construct($id, $command, $payment_id, $package_id);
		$this->conditions = $conditions;
		$this->player = $player;
	}

	public function getConditions() : TebexQueuedOfflineCommandConditions{
		return $this->conditions;
	}

	public function getPlayer() : TebexDuePlayer{
		return $this->player;
	}
}
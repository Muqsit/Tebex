<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;

final class TebexDuePlayerHolder{

	readonly private float $created;

	public function __construct(
		readonly private TebexDuePlayer $player
	){
		$this->created = microtime(true);
	}

	public function getPlayer() : TebexDuePlayer{
		return $this->player;
	}

	public function getCreated() : float{
		return $this->created;
	}
}
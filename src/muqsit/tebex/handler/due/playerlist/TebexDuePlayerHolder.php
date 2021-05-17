<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use muqsit\tebex\api\endpoint\queue\TebexDuePlayer;

final class TebexDuePlayerHolder{

	private float $created;
	private TebexDuePlayer $player;

	public function __construct(TebexDuePlayer $player){
		$this->player = $player;
		$this->created = microtime(true);
	}

	public function getPlayer() : TebexDuePlayer{
		return $this->player;
	}

	public function getCreated() : float{
		return $this->created;
	}
}
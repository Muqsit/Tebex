<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;

final class TebexDuePlayerHolder{

	readonly public float $created;

	public function __construct(
		readonly public TebexDuePlayer $player
	){
		$this->created = microtime(true);
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist\indexer;

use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use pocketmine\player\Player;

final class NameBasedPlayerIndexer implements PlayerIndexer{

	public function __construct(){
	}

	public function fromPlayer(Player $player) : string{
		return strtolower($player->getName());
	}

	public function fromTebexDuePlayer(TebexDuePlayer $player) : string{
		return strtolower($player->name);
	}
}
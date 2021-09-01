<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist\indexer;

use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use pocketmine\player\Player;

interface PlayerIndexer{

	public function fromPlayer(Player $player) : string;

	public function fromTebexDuePlayer(TebexDuePlayer $player) : string;
}
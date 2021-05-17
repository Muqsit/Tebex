<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use muqsit\tebex\handler\due\session\TebexPlayerSession;
use pocketmine\player\Player;

final class OfflineTebexDuePlayerList extends TebexDuePlayerList{

	/** @var int[] */
	private array $players = [];

	/** @var TebexPlayerSession[] */
	private array $online = [];

	private static function playerIndex(Player $player) : string{
		return strtolower($player->getName());
	}

	protected function onDuePlayerSet(TebexDuePlayerHolder $holder) : void{
		$player = $holder->getPlayer();
		$this->players[$index = strtolower($player->getName())] = $holder->getPlayer()->getId();
		if(isset($this->online[$index])){
			$this->onMatch($this->online[$index]->getPlayer(), $holder);
		}
	}

	public function get(Player $player) : ?TebexDuePlayerHolder{
		return isset($this->players[$index = self::playerIndex($player)]) ? $this->due_players[$this->players[$index]] : null;
	}

	protected function onDuePlayerRemove(TebexDuePlayerHolder $holder) : void{
		unset($this->players[strtolower($holder->getPlayer()->getName())]);
	}

	public function getSession(Player $player) : ?TebexPlayerSession{
		return $this->online[self::playerIndex($player)] ?? null;
	}

	public function onPlayerJoin(Player $player) : void{
		$this->online[self::playerIndex($player)] = new TebexPlayerSession($player);
		$holder = $this->get($player);
		if($holder !== null){
			$this->onMatch($player, $holder);
		}
	}

	public function onPlayerQuit(Player $player) : void{
		if(isset($this->online[$index = self::playerIndex($player)])){
			$this->online[$index]->destroy();
			unset($this->online[$index]);
		}
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use muqsit\tebex\handler\due\session\TebexPlayerSession;
use pocketmine\player\Player;
use RuntimeException;

final class OnlineTebexDuePlayerList extends TebexDuePlayerList{

	private static function getUuidFromTebexPlayer(TebexDuePlayer $player) : string{
		$uuid = $player->getUuid();
		if($uuid === null){
			throw new RuntimeException("Expected UUID to be non-null in Online Mode");
		}
		return $uuid;
	}

	/** @var int[] */
	private array $players = [];

	/** @var TebexPlayerSession[] */
	private array $online = [];

	private static function playerIndex(Player $player) : string{
		return $player->getXuid();
	}

	protected function onDuePlayerSet(TebexDuePlayerHolder $holder) : void{
		$player = $holder->getPlayer();
		$this->players[$index = self::getUuidFromTebexPlayer($player)] = $holder->getPlayer()->getId();
		if(isset($this->online[$index])){
			$this->onMatch($this->online[$index]->getPlayer(), $holder);
		}
	}

	protected function onDuePlayerRemove(TebexDuePlayerHolder $holder) : void{
		unset($this->players[self::getUuidFromTebexPlayer($holder->getPlayer())]);
	}

	public function get(Player $player) : ?TebexDuePlayerHolder{
		return isset($this->players[$index = self::playerIndex($player)]) ? $this->due_players[$this->players[$index]] : null;
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
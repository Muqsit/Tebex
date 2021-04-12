<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerslist;

use Closure;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayer;
use muqsit\tebex\handler\due\session\TebexPlayerSession;
use pocketmine\player\Player;

abstract class TebexDuePlayersList{

	/** @var TebexDuePlayerHolder[] */
	protected array $due_players = [];

	/** @phpstan-var Closure(Player, TebexDuePlayerHolder) : void */
	private Closure $on_match;

	/**
	 * @param Closure $on_match
	 * @phpstan-param Closure(Player, TebexDuePlayerHolder) : void $on_match
	 */
	public function __construct(Closure $on_match){
		$this->on_match = $on_match;
	}

	final protected function onMatch(Player $player, TebexDuePlayerHolder $holder) : void{
		($this->on_match)($player, $holder);
	}

	/**
	 * @return TebexDuePlayerHolder[]
	 */
	final public function getAll() : array{
		return $this->due_players;
	}

	/**
	 * @param TebexDuePlayer[] $due_players
	 */
	final public function update(array $due_players) : void{
		$this->due_players = [];
		foreach($due_players as $player){
			$holder = new TebexDuePlayerHolder($player);
			$this->due_players[$player->getId()] = $holder;
			$this->onDuePlayerSet($holder);
		}
	}

	final public function remove(TebexDuePlayerHolder $holder) : void{
		unset($this->due_players[$holder->getPlayer()->getId()]);
		$this->onDuePlayerRemove($holder);
	}

	abstract protected function onDuePlayerSet(TebexDuePlayerHolder $holder) : void;

	abstract protected function onDuePlayerRemove(TebexDuePlayerHolder $holder) : void;

	abstract public function get(Player $player) : ?TebexDuePlayerHolder;

	abstract public function getSession(Player $player) : ?TebexPlayerSession;

	abstract public function onPlayerJoin(Player $player) : void;

	abstract public function onPlayerQuit(Player $player) : void;
}
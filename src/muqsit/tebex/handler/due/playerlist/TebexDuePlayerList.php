<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use Closure;
use muqsit\tebex\handler\due\playerlist\indexer\PlayerIndexer;
use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use muqsit\tebex\handler\due\session\TebexPlayerSession;
use pocketmine\player\Player;

final class TebexDuePlayerList{

	private PlayerIndexer $indexer;

	/** @phpstan-var Closure(Player, TebexDuePlayerHolder) : void */
	private Closure $on_match;

	/**
	 * @var TebexDuePlayerHolder[]
	 *
	 * @phpstan-var array<int, TebexDuePlayerHolder>
	 */
	protected array $tebex_due_players_by_id = []; // indexes pending customers by TebexPlayerId => TebexDuePlayerHolder

	/**
	 * @var int[]
	 *
	 * @phpstan-var array<string, int>
	 */
	private array $tebex_due_players_by_index = []; // indexes pending customers by TebexDuePlayerList::$indexer => TebexPlayerId

	/**
	 * @var TebexPlayerSession[]
	 *
	 * @phpstan-var array<string, TebexPlayerSession>
	 */
	private array $online_players = []; // indexes online players on the server by TebexDuePlayerList::$indexer => TebexPlayerSession

	/**
	 * @param Closure $on_match
	 * @param PlayerIndexer $indexer
	 *
	 * @phpstan-param Closure(Player, TebexDuePlayerHolder) : void $on_match
	 */
	public function __construct(PlayerIndexer $indexer, Closure $on_match){
		$this->indexer = $indexer;
		$this->on_match = $on_match;
	}

	private function onMatch(Player $player, TebexDuePlayerHolder $holder) : void{
		($this->on_match)($player, $holder);
	}

	public function onPlayerJoin(Player $player) : void{
		$this->online_players[$this->indexer->fromPlayer($player)] = new TebexPlayerSession($player);
		$holder = $this->getTebexAwaitingPlayer($player);
		if($holder !== null){
			$this->onMatch($player, $holder);
		}
	}

	public function onPlayerQuit(Player $player) : void{
		if(isset($this->online_players[$index = $this->indexer->fromPlayer($player)])){
			$this->online_players[$index]->destroy();
			unset($this->online_players[$index]);
		}
	}

	/**
	 * @return TebexDuePlayerHolder[]
	 */
	public function getAll() : array{
		return $this->tebex_due_players_by_id;
	}

	/**
	 * @param TebexDuePlayer[] $due_players
	 */
	public function update(array $due_players) : void{
		$this->tebex_due_players_by_id = [];
		$this->tebex_due_players_by_index = [];
		foreach($due_players as $player){
			$holder = new TebexDuePlayerHolder($player);
			$this->tebex_due_players_by_id[$player->getId()] = $holder;
			$this->tebex_due_players_by_index[$index = $this->indexer->fromTebexDuePlayer($player)] = $player->getId();
			if(isset($this->online_players[$index])){
				$this->onMatch($this->online_players[$index]->getPlayer(), $holder);
			}
		}
	}

	public function remove(TebexDuePlayerHolder $holder) : void{
		$player = $holder->getPlayer();
		unset($this->tebex_due_players_by_id[$player->getId()], $this->tebex_due_players_by_index[$this->indexer->fromTebexDuePlayer($player)]);
	}

	public function getTebexAwaitingPlayer(Player $player) : ?TebexDuePlayerHolder{
		return isset($this->tebex_due_players_by_index[$index = $this->indexer->fromPlayer($player)]) ? $this->tebex_due_players_by_id[$this->tebex_due_players_by_index[$index]] : null;
	}

	public function getOnlinePlayer(Player $player) : ?TebexPlayerSession{
		return $this->online_players[$this->indexer->fromPlayer($player)] ?? null;
	}
}
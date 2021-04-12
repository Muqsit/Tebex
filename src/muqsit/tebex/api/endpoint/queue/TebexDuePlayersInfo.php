<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue;

use muqsit\tebex\api\connection\response\TebexResponse;

final class TebexDuePlayersInfo implements TebexResponse{

	private TebexDuePlayersMeta $meta;

	/** @var TebexDuePlayer[] */
	private array $players;

	/**
	 * @param TebexDuePlayersMeta $meta
	 * @param TebexDuePlayer[] $players
	 */
	public function __construct(TebexDuePlayersMeta $meta, array $players){
		$this->meta = $meta;
		$this->players = $players;
	}

	public function getMeta() : TebexDuePlayersMeta{
		return $this->meta;
	}

	/**
	 * @return TebexDuePlayer[]
	 */
	public function getPlayers() : array{
		return $this->players;
	}
}
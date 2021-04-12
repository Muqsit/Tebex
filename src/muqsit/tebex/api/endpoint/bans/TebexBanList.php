<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\bans;

use muqsit\tebex\api\connection\response\TebexResponse;

final class TebexBanList implements TebexResponse{

	/** @var TebexBanEntry[] */
	private array $entries;

	/**
	 * @param TebexBanEntry[] $entries
	 */
	public function __construct(array $entries){
		$this->entries = $entries;
	}

	/**
	 * @return TebexBanEntry[]
	 */
	public function getAll() : array{
		return $this->entries;
	}
}
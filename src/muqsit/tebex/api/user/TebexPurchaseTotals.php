<?php

declare(strict_types=1);

namespace muqsit\tebex\api\user;

final class TebexPurchaseTotals{

	/** @var array<string, float> */
	private $purchase_totals;

	/**
	 * @param array<string, float> $purchase_totals
	 */
	public function __construct(array $purchase_totals){
		$this->purchase_totals = $purchase_totals;
	}

	/**
	 * @return array<string, float>
	 */
	public function getAll() : array{
		return $this->purchase_totals;
	}

	public function get(string $currency) : ?float{
		return $this->purchase_totals[$currency] ?? null;
	}
}
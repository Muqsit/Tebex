<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\sales;

use muqsit\tebex\api\connection\response\TebexResponse;

final class TebexSalesList implements TebexResponse{

	/** @var TebexSale[] */
	private array $sales;

	/**
	 * @param TebexSale[] $sales
	 */
	public function __construct(array $sales){
		$this->sales = $sales;
	}

	/**
	 * @return TebexSale[]
	 */
	public function getAll() : array{
		return $this->sales;
	}
}
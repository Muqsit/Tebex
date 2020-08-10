<?php

declare(strict_types=1);

namespace muqsit\tebex\api\listing;

final class TebexPackageSaleInfo{

	/** @var bool */
	private $active;

	/** @var string */
	private $discount;

	public function __construct(bool $active, string $discount){
		$this->active = $active;
		$this->discount = $discount;
	}

	public function isActive() : bool{
		return $this->active;
	}

	public function getDiscount() : string{
		return $this->discount;
	}

	public function getPostDiscountPrice(string $price) : string{
		return $this->active ? sprintf("%0.2f", ((float) $price) - ((float) $this->discount)) : $price;
	}
}
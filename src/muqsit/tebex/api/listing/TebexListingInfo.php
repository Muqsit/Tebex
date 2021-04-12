<?php

declare(strict_types=1);

namespace muqsit\tebex\api\listing;

use muqsit\tebex\api\TebexResponse;

final class TebexListingInfo implements TebexResponse{

	/** @var TebexCategory[] */
	private array $categories;

	/**
	 * @param TebexCategory[] $categories
	 */
	public function __construct(array $categories){
		$this->categories = $categories;
	}

	/**
	 * @return TebexCategory[]
	 */
	public function getCategories() : array{
		return $this->categories;
	}
}
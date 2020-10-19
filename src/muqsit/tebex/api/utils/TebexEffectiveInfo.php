<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

final class TebexEffectiveInfo{

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 */
	public static function fromTebexResponse(array $response) : self{
		/** @var array{type: string, packages: int[], categories: int[]} $response */
		return new self(
			$response["type"],
			$response["packages"],
			$response["categories"]
		);
	}

	/** @var string */
	private $type;

	/** @var int[] */
	private $package_ids;

	/** @var int[] */
	private $category_ids;

	/**
	 * @param string $type
	 * @param int[] $package_ids
	 * @param int[] $category_ids
	 */
	public function __construct(string $type, array $package_ids, array $category_ids){
		$this->type = $type;
		$this->package_ids = $package_ids;
		$this->category_ids = $category_ids;
	}

	public function getType() : string{
		return $this->type;
	}

	/**
	 * @return int[]
	 */
	public function getPackageIds() : array{
		return $this->package_ids;
	}

	/**
	 * @return int[]
	 */
	public function getCategoryIds() : array{
		return $this->category_ids;
	}
}
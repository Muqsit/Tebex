<?php

declare(strict_types=1);

namespace muqsit\tebex\api\package;

use muqsit\tebex\api\TebexResponse;

final class TebexPackages implements TebexResponse{

	/** @var TebexPackage[] */
	private $packages;

	/**
	 * @param TebexPackage[] $packages
	 */
	public function __construct(array $packages){
		$this->packages = $packages;
	}

	/**
	 * @return TebexPackage[]
	 */
	public function getAll() : array{
		return $this->packages;
	}
}
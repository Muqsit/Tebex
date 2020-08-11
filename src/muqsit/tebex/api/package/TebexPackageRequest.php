<?php

declare(strict_types=1);

namespace muqsit\tebex\api\package;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexPackage>
 */
final class TebexPackageRequest extends TebexGETRequest{

	/** @var int */
	private $package_id;

	public function __construct(int $package_id){
		$this->package_id = $package_id;
	}

	public function getEndpoint() : string{
		return "/packages/{$this->package_id}";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		return TebexPackage::fromTebexResponse($response);
	}
}
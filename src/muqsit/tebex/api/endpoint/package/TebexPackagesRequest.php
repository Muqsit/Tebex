<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\package;

use muqsit\tebex\api\connection\request\TebexGETRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexPackages>
 */
final class TebexPackagesRequest extends TebexGETRequest{

	public function getEndpoint() : string{
		return "/packages";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array<string, mixed>[] $response
	 * @return TebexResponse
	 */
	public function createResponse(array $response) : TebexResponse{
		$packages = [];
		foreach($response as $package_response){
			$packages[] = TebexPackage::fromTebexResponse($package_response);
		}
		return new TebexPackages($packages);
	}
}
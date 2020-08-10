<?php

declare(strict_types=1);

namespace muqsit\tebex\api\package;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexPackagesRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/packages";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		$packages = [];
		foreach($response as $package_response){
			$packages[] = TebexPackage::fromTebexResponse($package_response);
		}
		return new TebexPackages($packages);
	}
}
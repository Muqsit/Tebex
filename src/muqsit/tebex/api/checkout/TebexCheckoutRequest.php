<?php

declare(strict_types=1);

namespace muqsit\tebex\api\checkout;

use muqsit\tebex\api\RespondingTebexRequest;
use muqsit\tebex\api\TebexPOSTRequest;
use muqsit\tebex\api\TebexResponse;

final class TebexCheckoutRequest extends TebexPOSTRequest implements RespondingTebexRequest{

	/** @var int */
	private $package_id;

	/** @var string */
	private $username;

	public function __construct(int $package_id, string $username){
		$this->package_id = $package_id;
		$this->username = $username;
	}

	public function getEndpoint() : string{
		return "/checkout?" . http_build_query([
			"package_id" => $this->package_id,
			"username" => $this->username
		]);
	}

	public function getExpectedResponseCode() : int{
		return 201;
	}

	protected function getPOSTFields() : string{
		return "";
	}

	public function createResponse(array $response) : TebexResponse{
		return new TebexCheckoutInfo($response["url"], $response["expires"]);
	}
}
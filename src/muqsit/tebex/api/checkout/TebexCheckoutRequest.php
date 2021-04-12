<?php

declare(strict_types=1);

namespace muqsit\tebex\api\checkout;

use muqsit\tebex\api\TebexPOSTRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexPOSTRequest<TebexCheckoutInfo>
 */
final class TebexCheckoutRequest extends TebexPOSTRequest{

	private int $package_id;
	private string $username;

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

	/**
	 * @param array $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{url: string, expires: string} $response
	 */
	public function createResponse(array $response) : TebexResponse{
		return new TebexCheckoutInfo($response["url"], $response["expires"]);
	}
}
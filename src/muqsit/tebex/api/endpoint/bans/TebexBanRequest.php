<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\bans;

use muqsit\tebex\api\connection\request\TebexPostRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexPostRequest<TebexBanEntry>
 */
final class TebexBanRequest extends TebexPostRequest{

	private string $username_or_uuid;
	private ?string $reason;
	private ?string $ip;

	public function __construct(string $username_or_uuid, ?string $reason = null, ?string $ip = null){
		$this->username_or_uuid = $username_or_uuid;
		$this->reason = $reason;
		$this->ip = $ip;
	}

	public function getEndpoint() : string{
		return "/bans";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array{data: array} $response
	 * @return TebexResponse
	 */
	public function createResponse(array $response) : TebexResponse{
		return TebexBanEntry::fromTebexResponse($response["data"]);
	}

	protected function getPOSTFields() : string{
		return http_build_query([
			"user" => $this->username_or_uuid,
			"ip" => $this->ip,
			"reason" => $this->reason
		]);
	}
}
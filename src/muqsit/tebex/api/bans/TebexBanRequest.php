<?php

declare(strict_types=1);

namespace muqsit\tebex\api\bans;

use muqsit\tebex\api\TebexPOSTRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexBanRequest extends TebexPOSTRequest implements RespondingTebexRequest{

	/** @var string */
	private $username_or_uuid;

	/** @var string|null */
	private $reason;

	/** @var string|null */
	private $ip;

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
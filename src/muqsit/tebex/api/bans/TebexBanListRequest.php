<?php

declare(strict_types=1);

namespace muqsit\tebex\api\bans;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexBanListRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/bans";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		$entries = [];
		foreach($response["data"] as $entry){
			$entries[] = TebexBanEntry::fromTebexResponse($entry);
		}
		return new TebexBanList($entries);
	}
}
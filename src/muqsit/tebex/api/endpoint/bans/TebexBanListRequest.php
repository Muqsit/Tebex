<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\bans;

use muqsit\tebex\api\connection\request\TebexGETRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexBanList>
 */
final class TebexBanListRequest extends TebexGETRequest{

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
		$entries = [];
		foreach($response["data"] as $entry){
			$entries[] = TebexBanEntry::fromTebexResponse($entry);
		}
		return new TebexBanList($entries);
	}
}
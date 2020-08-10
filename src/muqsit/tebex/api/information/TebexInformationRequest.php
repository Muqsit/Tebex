<?php

declare(strict_types=1);

namespace muqsit\tebex\api\information;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexInformationRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/information";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		["account" => $account, "server" => $server] = $response;
		return new TebexInformation(
			new TebexAccountInformation(
				$account["id"],
				$account["domain"],
				$account["name"],
				new TebexAccountCurrencyInformation(
					$account["currency"]["iso_4217"],
					$account["currency"]["symbol"],
				),
				$account["online_mode"],
				$account["game_type"],
				$account["log_events"]
			),
			new TebexServerInformation(
				$server["id"],
				$server["name"]
			)
		);
	}
}
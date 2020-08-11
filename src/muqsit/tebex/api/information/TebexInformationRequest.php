<?php

declare(strict_types=1);

namespace muqsit\tebex\api\information;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexInformation>
 */
final class TebexInformationRequest extends TebexGETRequest{

	public function getEndpoint() : string{
		return "/information";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{
	 * 		account: array{
	 * 			id: int,
	 * 			domain: string,
	 * 			name: string,
	 * 			currency: array{iso_4217: string, symbol: string},
	 * 			online_mode: bool,
	 * 			game_type: string,
	 * 			log_events: bool
	 * 		},
	 * 		server: array{id: int, name: string}
	 * } $response
	 */
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
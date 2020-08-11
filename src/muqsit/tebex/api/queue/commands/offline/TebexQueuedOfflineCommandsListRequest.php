<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue\commands\offline;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\queue\TebexDuePlayer;

/**
 * @phpstan-extends TebexGETRequest<TebexQueuedOfflineCommandsInfo>
 */
final class TebexQueuedOfflineCommandsListRequest extends TebexGETRequest{

	public function getEndpoint() : string{
		return "/queue/offline-commands";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array<string, mixed> $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{
	 * 		commands: array<array{
	 * 			id: int,
	 * 			player: array<string, mixed>,
	 * 			command: string,
	 * 			payment: int,
	 * 			package: int,
	 * 			conditions: array{delay: int}
	 * 		}>,
	 * 		meta: array{limited: bool}
	 * } $response
	 */
	public function createResponse(array $response) : TebexResponse{
		$commands = [];
		foreach($response["commands"] as $cmd){
			$commands[] = new TebexQueuedOfflineCommand(
				$cmd["id"],
				$cmd["command"],
				$cmd["payment"],
				$cmd["package"],
				new TebexQueuedOfflineCommandConditions($cmd["conditions"]["delay"]),
				TebexDuePlayer::fromTebexResponse($cmd["player"])
			);
		}

		return new TebexQueuedOfflineCommandsInfo(
			new TebexQueuedOfflineCommandsMeta($response["meta"]["limited"]),
			$commands
		);
	}
}
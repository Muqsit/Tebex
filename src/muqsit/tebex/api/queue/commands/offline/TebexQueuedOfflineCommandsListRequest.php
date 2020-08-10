<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue\commands\offline;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\queue\TebexDuePlayer;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexQueuedOfflineCommandsListRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/queue/offline-commands";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		$commands = [];
		foreach($response["commands"] as $cmd){
			$cmd["player"]["id"] = (int) $cmd["player"]["id"];
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
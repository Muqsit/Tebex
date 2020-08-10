<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexDuePlayersListRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/queue";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		["meta" => $meta, "players" => $players_list] = $response;

		$players = [];
		foreach($players_list as $player){
			$players[] = TebexDuePlayer::fromTebexResponse($player);
		}

		return new TebexDuePlayersInfo(
			new TebexDuePlayersMeta(
				$meta["execute_offline"],
				$meta["next_check"],
				$meta["more"]
			),
			$players
		);
	}
}
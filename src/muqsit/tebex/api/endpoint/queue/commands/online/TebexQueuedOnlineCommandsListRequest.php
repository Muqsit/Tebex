<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue\commands\online;

use muqsit\tebex\api\connection\request\TebexGETRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexQueuedOnlineCommandsInfo>
 */
final class TebexQueuedOnlineCommandsListRequest extends TebexGETRequest{

	private int $player_id;

	public function __construct(int $player_id){
		$this->player_id = $player_id;
	}

	public function getEndpoint() : string{
		return "/queue/online-commands/{$this->player_id}";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{
	 * 		commands: array<array{
	 * 			id: int,
	 * 			command: string,
	 * 			payment: int,
	 * 			package: int,
	 * 			conditions: array{delay: int, slots: int}
	 * 		}>
	 * } $response
	 */
	public function createResponse(array $response) : TebexResponse{
		$commands = [];
		foreach($response["commands"] as $cmd){
			$commands[] = new TebexQueuedOnlineCommand(
				$cmd["id"],
				$cmd["command"],
				$cmd["payment"],
				$cmd["package"],
				new TebexQueuedOnlineCommandConditions($cmd["conditions"]["delay"], $cmd["conditions"]["slots"])
			);
		}

		return new TebexQueuedOnlineCommandsInfo($commands);
	}
}
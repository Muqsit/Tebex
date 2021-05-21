<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue\commands;

use muqsit\tebex\api\connection\request\TebexDeleteRequest;
use muqsit\tebex\api\connection\response\EmptyTebexResponse;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexDeleteRequest<EmptyTebexResponse>
 */
final class TebexDeleteCommandRequest extends TebexDeleteRequest{

	/** @var int[] */
	private array $command_ids;

	/**
	 * @param int[] $command_ids
	 */
	public function __construct(array $command_ids){
		$this->command_ids = $command_ids;
	}

	public function getEndpoint() : string{
		return "/queue";
	}

	public function getExpectedResponseCode() : int{
		return 204;
	}

	protected function getPOSTFields() : string{
		return http_build_query([
			"ids" => $this->command_ids
		]);
	}

	public function createResponse(array $response) : TebexResponse{
		return EmptyTebexResponse::instance();
	}
}
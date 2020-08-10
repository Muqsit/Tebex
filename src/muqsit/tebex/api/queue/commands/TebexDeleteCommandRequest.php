<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue\commands;

use muqsit\tebex\api\TebexDELETERequest;

final class TebexDeleteCommandRequest extends TebexDELETERequest{

	/** @var int[] */
	private $command_ids;

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
}
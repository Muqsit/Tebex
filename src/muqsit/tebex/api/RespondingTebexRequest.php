<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

interface RespondingTebexRequest{

	/**
	 * Creates a response object out of JSON formatted response
	 * data obtained from the API.
	 *
	 * @param mixed[] $response
	 * @return TebexResponse
	 */
	public function createResponse(array $response) : TebexResponse;
}
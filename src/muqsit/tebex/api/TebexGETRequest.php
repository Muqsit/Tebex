<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

/**
 * @phpstan-template TTebexResponse of TebexResponse
 * @phpstan-implements TebexRequest<TTebexResponse>
 */
abstract class TebexGETRequest implements TebexRequest{

	public function addAdditionalCurlOpts(array &$curl_opts) : void{
	}
}
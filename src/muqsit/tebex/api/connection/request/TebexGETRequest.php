<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection\request;

/**
 * @phpstan-template TTebexResponse of \muqsit\tebex\api\connection\response\TebexResponse
 * @phpstan-implements TebexRequest<TTebexResponse>
 */
abstract class TebexGETRequest implements TebexRequest{

	public function addAdditionalCurlOpts(array &$curl_opts) : void{
	}
}
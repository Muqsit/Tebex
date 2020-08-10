<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

abstract class TebexGETRequest implements TebexRequest{

	public function addAdditionalCurlOpts(array &$curl_opts) : void{
	}
}
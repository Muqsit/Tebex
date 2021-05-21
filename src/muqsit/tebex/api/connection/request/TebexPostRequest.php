<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection\request;

/**
 * @phpstan-template TTebexResponse of \muqsit\tebex\api\connection\response\TebexResponse
 * @phpstan-implements TebexRequest<TTebexResponse>
 */
abstract class TebexPostRequest implements TebexRequest{

	public function addAdditionalCurlOpts(array &$curl_opts) : void{
		$data = $this->getPOSTFields();
		$curl_opts[CURLOPT_POST] = true;
		$curl_opts[CURLOPT_CUSTOMREQUEST] = "POST";
		$curl_opts[CURLOPT_POSTFIELDS] = $data;
		assert(is_array($curl_opts[CURLOPT_HTTPHEADER]));
		array_push($curl_opts[CURLOPT_HTTPHEADER],
			"Content-Type: application/json",
			"Content-Length: " . strlen($data)
		);
	}

	abstract protected function getPOSTFields() : string;
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection;

final class TebexConnectionHelper{

	/**
	 * @return array<int, mixed>
	 */
	public static function buildDefaultCurlOptions(string $secret, string $ca_path) : array{
		$curl_opts = [
			CURLOPT_HTTPHEADER => [
				"X-Tebex-Secret: {$secret}",
				"User-Agent: Tebex"
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 5,
		];
		if($ca_path !== ""){
			$curl_opts[CURLOPT_CAINFO] = $ca_path;
		}
		return $curl_opts;
	}
}
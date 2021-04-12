<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection\handler;

use muqsit\tebex\api\connection\request\TebexRequestHolder;
use muqsit\tebex\api\connection\response\TebexResponse;
use muqsit\tebex\api\connection\response\TebexResponseHolder;

interface TebexConnectionHandler{

	/**
	 * @param TebexRequestHolder $request_holder
	 * @param mixed[] $default_curl_options
	 * @return TebexResponseHolder<TebexResponse>
	 *
	 * @phpstan-param array<int, mixed> $default_curl_options
	 */
	public function handle(TebexRequestHolder $request_holder, array $default_curl_options) : TebexResponseHolder;
}
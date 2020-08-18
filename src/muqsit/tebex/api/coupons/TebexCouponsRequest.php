<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexCouponsList>
 */
final class TebexCouponsRequest extends TebexGETRequest{

	public function getEndpoint() : string{
		return "/coupons";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array<string, mixed> $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{data: array<string, mixed>} $response
	 */
	public function createResponse(array $response) : TebexResponse{
		return TebexCouponsList::fromTebexResponse($response);
	}
}
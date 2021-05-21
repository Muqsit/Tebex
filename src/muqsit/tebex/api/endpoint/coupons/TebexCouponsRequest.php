<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\coupons;

use muqsit\tebex\api\connection\request\TebexGetRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexGetRequest<TebexCouponsList>
 */
final class TebexCouponsRequest extends TebexGetRequest{

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
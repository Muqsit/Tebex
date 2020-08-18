<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexCoupon>
 */
final class TebexCouponRequest extends TebexGETRequest{

	/** @var int */
	private $coupon_id;

	public function __construct(int $coupon_id){
		$this->coupon_id = $coupon_id;
	}

	public function getEndpoint() : string{
		return "/coupons/{$this->coupon_id}";
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
		return TebexCoupon::fromTebexResponse($response["data"]);
	}
}
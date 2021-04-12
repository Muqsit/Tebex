<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\coupons\create;

use muqsit\tebex\api\connection\request\TebexPOSTRequest;
use muqsit\tebex\api\connection\response\TebexResponse;

/**
 * @phpstan-extends TebexPOSTRequest<TebexCouponCreateResponse>
 */
final class TebexCouponCreateRequest extends TebexPOSTRequest{

	private TebexCreatedCoupon $coupon;

	public function __construct(TebexCreatedCoupon $coupon){
		$this->coupon = $coupon;
	}

	public function getEndpoint() : string{
		return "/coupons?" . http_build_query($this->coupon->toHTTPResponseArray());
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
		return TebexCouponCreateResponse::fromTebexResponse($response["data"]);
	}

	protected function getPOSTFields() : string{
		return "";
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons\create;

use muqsit\tebex\api\TebexPOSTRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\utils\TebexTypeUtils;

/**
 * @phpstan-extends TebexPOSTRequest<TebexCouponCreateResponse>
 */
final class TebexCouponCreateRequest extends TebexPOSTRequest{

	/** @var TebexCreatedCoupon */
	private $coupon;

	public function __construct(TebexCreatedCoupon $coupon){
		$this->coupon = $coupon;
	}

	public function getEndpoint() : string{
		return "/coupons?" . http_build_query(array_map(function($v){
			return is_bool($v) ? TebexTypeUtils::booleanToString($v) : $v;
		}, $this->coupon->toHTTPResponseArray()));
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
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\EmptyTebexResponse;
use muqsit\tebex\api\TebexDELETERequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexDELETERequest<EmptyTebexResponse>
 */
final class TebexCouponDeleteRequest extends TebexDELETERequest{

	/** @var int */
	private $coupon_id;

	/**
	 * @param int $coupon_id
	 */
	public function __construct(int $coupon_id){
		$this->coupon_id = $coupon_id;
	}

	public function getEndpoint() : string{
		return "/coupons/{$this->coupon_id}";
	}

	public function getExpectedResponseCode() : int{
		return 204;
	}

	protected function getPOSTFields() : string{
		return http_build_query([
			"id" => $this->coupon_id
		]);
	}

	public function createResponse(array $response) : TebexResponse{
		return EmptyTebexResponse::instance();
	}
}
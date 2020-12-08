<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexCouponsList>
 */
final class TebexCouponsRequest extends TebexGETRequest{

	/** @var int */
	private $page;

	public function __construct(int $page){
		$this->page = $page;
	}

	public function getEndpoint() : string{
		return "/coupons?" . http_build_query([
			"page" => $this->page
		]);
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
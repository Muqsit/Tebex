<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\TebexResponse;

final class TebexCouponsList implements TebexResponse{

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 *
	 * @phpstan-param  array{data: array<array<string, mixed>>} $response
	 */
	public static function fromTebexResponse(array $response) : self{
		/**
		 * @var array{
		 * 		pagination: array<string, mixed>,
		 * 		data: array<array<string, mixed>>
		 * } $response
		 */

		$coupons = [];
		foreach($response["data"] as $coupon_data){
			$coupon = TebexCoupon::fromTebexResponse($coupon_data);
			$coupons[$coupon->getId()] = $coupon;
		}
		return new self(TebexCouponsListPagination::fromTebexResponse($response["pagination"]), $coupons);
	}

	private TebexCouponsListPagination $pagination;

	/** @var TebexCoupon[] */
	private array $coupons;

	/**
	 * @param TebexCouponsListPagination $pagination
	 * @param TebexCoupon[] $coupons
	 */
	public function __construct(TebexCouponsListPagination $pagination, array $coupons){
		$this->pagination = $pagination;
		$this->coupons = $coupons;
	}

	public function getPagination() : TebexCouponsListPagination{
		return $this->pagination;
	}

	/**
	 * @return TebexCoupon[]
	 */
	public function getCoupons() : array{
		return $this->coupons;
	}

	public function getCoupon(int $id) : ?TebexCoupon{
		return $this->coupons[$id] ?? null;
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

final class TebexCouponExpireInfo{

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 */
	public static function fromTebexResponse(array $response) : self{
		/**
		 * @phpstan-var array{
		 * 		redeem_unlimited: string,
		 * 		expire_never: string,
		 * 		limit: int,
		 * 		date: string
		 * } $response
		 */

		return new self(
			(bool) $response["redeem_unlimited"],
			(bool) $response["expire_never"],
			$response["limit"],
			(int) strtotime($response["date"])
		);
	}

	private bool $redeem_unlimited;
	private bool $expire_never;
	private int $limit;
	private int $date;

	public function __construct(bool $redeem_unlimited, bool $expire_never, int $limit, int $date){
		$this->redeem_unlimited = $redeem_unlimited;
		$this->expire_never = $expire_never;
		$this->limit = $limit;
		$this->date = $date;
	}

	public function isRedeemLimited() : bool{
		return !$this->redeem_unlimited;
	}

	public function canExpire() : bool{
		return !$this->expire_never;
	}

	public function getLimit() : int{
		return $this->limit;
	}

	public function getDate() : int{
		return $this->date;
	}
}
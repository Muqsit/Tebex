<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\utils\TebexTypeUtils;

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
			TebexTypeUtils::stringToBoolean($response["redeem_unlimited"]),
			TebexTypeUtils::stringToBoolean($response["expire_never"]),
			$response["limit"],
			(int) strtotime($response["date"])
		);
	}

	/** @var bool */
	private $redeem_unlimited;

	/** @var bool */
	private $expire_never;

	/** @var int */
	private $limit;

	/** @var int */
	private $date;

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
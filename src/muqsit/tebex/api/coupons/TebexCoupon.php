<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons;

use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\TebexEffectiveInfo;

final class TebexCoupon implements TebexResponse{

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 */
	public static function fromTebexResponse(array $response) : self{
		/**
		 * @var array{
		 * 		id: int,
		 * 		code: string,
		 * 		effective: array<string, mixed>,
		 * 		discount: array<string, mixed>,
		 * 		expire: array<string, mixed>,
		 * 		basket_type: string,
		 * 		start_date: string,
		 * 		user_limit: int,
		 * 		minimum: int,
		 * 		username: string,
		 * 		note: string
		 * } $response
		 */

		var_dump($response);
		return new self(
			$response["id"],
			$response["code"],
			TebexEffectiveInfo::fromTebexResponse($response["effective"]),
			TebexDiscountInfo::fromTebexResponse($response["discount"]),
			TebexCouponExpireInfo::fromTebexResponse($response["expire"]),
			$response["basket_type"],
			(int) strtotime($response["start_date"]),
			$response["user_limit"],
			$response["minimum"],
			$response["username"] !== "" ? $response["username"] : null,
			$response["note"]
		);
	}

	/** @var int */
	private $id;

	/** @var string */
	private $code;

	/** @var TebexEffectiveInfo */
	private $effective;

	/** @var TebexDiscountInfo */
	private $discount;

	/** @var TebexCouponExpireInfo */
	private $expire;

	/** @var string */
	private $basket_type;

	/** @var int */
	private $start_date;

	/** @var int */
	private $user_limit;

	/** @var int */
	private $minimum;

	/** @var string|null */
	private $username;

	/** @var string */
	private $note;

	public function __construct(int $id, string $code, TebexEffectiveInfo $effective, TebexDiscountInfo $discount, TebexCouponExpireInfo $expire, string $basket_type, int $start_date, int $user_limit, int $minimum, ?string $username, string $note){
		$this->id = $id;
		$this->code = $code;
		$this->effective = $effective;
		$this->discount = $discount;
		$this->expire = $expire;
		$this->basket_type = $basket_type;
		$this->start_date = $start_date;
		$this->user_limit = $user_limit;
		$this->minimum = $minimum;
		$this->username = $username;
		$this->note = $note;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getCode() : string{
		return $this->code;
	}

	public function getEffective() : TebexEffectiveInfo{
		return $this->effective;
	}

	public function getDiscount() : TebexDiscountInfo{
		return $this->discount;
	}

	public function getExpire() : TebexCouponExpireInfo{
		return $this->expire;
	}

	public function getBasketType() : string{
		return $this->basket_type;
	}

	public function getStartDate() : int{
		return $this->start_date;
	}

	public function getMinimum() : int{
		return $this->minimum;
	}

	public function getUsername() : ?string{
		return $this->username;
	}

	public function getUserLimit() : int{
		return $this->user_limit;
	}

	public function getNote() : string{
		return $this->note;
	}
}
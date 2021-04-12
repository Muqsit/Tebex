<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons\create;

use muqsit\tebex\api\coupons\TebexCouponExpireInfo;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\TebexEffectiveInfo;

final class TebexCouponCreateResponse implements TebexResponse{

	/**
	 * @param array<string, mixed> $response
	 * @return self
	 */
	public static function fromTebexResponse(array $response) : self{
		/**
		 * @phpstan-var array{
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

		$effective = TebexEffectiveInfo::fromTebexResponse($response["effective"]);
		$discount = TebexDiscountInfo::fromTebexResponse($response["discount"]);
		$expire = TebexCouponExpireInfo::fromTebexResponse($response["expire"]);

		return new self(
			$response["id"],
			$response["code"],
			$effective->getType(),
			$effective->getPackageIds(),
			$effective->getCategoryIds(),
			$discount->getType(),
			$discount->getValue(),
			$discount->getPercentage(),
			!$expire->isRedeemLimited(),
			!$expire->canExpire(),
			$expire->getLimit(),
			(string) date("Y-m-d", $expire->getDate()),
			(string) date("Y-m-d", (int) strtotime($response["start_date"])),
			$response["basket_type"],
			$response["minimum"],
			$response["username"],
			$response["note"]
		);
	}

	public int $id;
	public string $code;
	public string $effective_on;

	/** @var int[] */
	public array $packages;

	/** @var int[] */
	public array $categories;

	public string $discount_type;
	public int $discount_amount;
	public int $discount_percentage;
	public bool $redeem_unlimited;
	public bool $expire_never;
	public int $expire_limit;
	public string $expire_date; // yyyy-mm-dd
	public string $start_date;
	public string $basket_type;
	public int $minimum;
	public string $username = "";
	public string $note;

	/**
	 * @param int $id
	 * @param string $code
	 * @param string $effective_on
	 * @param int[] $packages
	 * @param int[] $categories
	 * @param string $discount_type
	 * @param int $discount_amount
	 * @param int $discount_percentage
	 * @param bool $redeem_unlimited
	 * @param bool $expire_never
	 * @param int $expire_limit
	 * @param string $expire_date
	 * @param string $start_date
	 * @param string $basket_type
	 * @param int $minimum
	 * @param string $username
	 * @param string $note
	 */
	public function __construct(
		int $id,
		string $code,
		string $effective_on,
		array $packages,
		array $categories,
		string $discount_type,
		int $discount_amount,
		int $discount_percentage,
		bool $redeem_unlimited,
		bool $expire_never,
		int $expire_limit,
		string $expire_date,
		string $start_date,
		string $basket_type,
		int $minimum,
		string $username,
		string $note
	){
		$this->id = $id;
		$this->code = $code;
		$this->packages = $packages;
		$this->categories = $categories;
		$this->effective_on = $effective_on;
		$this->discount_type = $discount_type;
		$this->discount_amount = $discount_amount;
		$this->discount_percentage = $discount_percentage;
		$this->redeem_unlimited = $redeem_unlimited;
		$this->expire_never = $expire_never;
		$this->expire_limit = $expire_limit;
		$this->expire_date = $expire_date;
		$this->start_date = $start_date;
		$this->basket_type = $basket_type;
		$this->minimum = $minimum;
		$this->username = $username;
		$this->note = $note;
	}
}
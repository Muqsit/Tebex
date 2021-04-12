<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons\create;

class TebexCreatedCoupon{

	public const EFFECTIVE_ON_PACKAGE = "package";
	public const EFFECTIVE_ON_CATEGORY = "category";

	public const DISCOUNT_TYPE_PERCENTAGE = "percentage";
	public const DISCOUNT_TYPE_VALUE = "value";

	public const BASKET_TYPE_SINGLE = "single";
	public const BASKET_TYPE_SUBSCRIPTION = "subscription";
	public const BASKET_TYPE_BOTH = "both";

	public const DISCOUNT_APP_METHOD_APPLY_EACH_PACKAGE = 0;
	public const DISCOUNT_APP_METHOD_APPLY_BASKET_BEFORE_SALE = 1;
	public const DISCOUNT_APP_METHOD_APPLY_BASKET_AFTER_SALE = 2;

	public static function createWithDefaultParameters() : self{
		return new self(
			implode("-", str_split(uniqid(), 4)),
			self::EFFECTIVE_ON_PACKAGE,
			self::DISCOUNT_TYPE_VALUE,
			0,
			0,
			true,
			false,
			0,
			date("Y-m-d"),
			date("Y-m-d"),
			self::BASKET_TYPE_BOTH,
			0,
			self::DISCOUNT_APP_METHOD_APPLY_EACH_PACKAGE,
			""
		);
	}

	public string $code;
	public string $effective_on;

	/** @var int[] */
	public array $packages = [];

	/** @var int[] */
	public array $categories = [];

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
	public int $discount_application_method;
	public string $username = "";
	public string $note;

	public function __construct(
		string $code,
		string $effective_on,
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
		int $discount_application_method,
		string $note
	){
		$this->code = $code;
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
		$this->discount_application_method = $discount_application_method;
		$this->note = $note;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toHTTPResponseArray() : array{
		return [
			"code" => $this->code,
			"effective_on" => $this->effective_on,
			"packages" => $this->packages,
			"categories" => $this->categories,
			"discount_type" => $this->discount_type,
			"discount_amount" => $this->discount_amount,
			"discount_percentage" => $this->discount_percentage,
			"redeem_unlimited" => $this->redeem_unlimited,
			"expire_never" => $this->expire_never,
			"expire_limit" => $this->expire_limit,
			"expire_date" => $this->expire_date,
			"start_date" => $this->start_date,
			"basket_type" => $this->basket_type,
			"minimum" => $this->minimum,
			"discount_application_method" => $this->discount_application_method,
			"username" => $this->username,
			"note" => $this->note
		];
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\coupons\create;

use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\TebexEffectiveInfo;

class TebexCreatedCoupon{

	public const BASKET_TYPE_SINGLE = "single";
	public const BASKET_TYPE_SUBSCRIPTION = "subscription";
	public const BASKET_TYPE_BOTH = "both";

	public const DISCOUNT_APP_METHOD_APPLY_EACH_PACKAGE = 0;
	public const DISCOUNT_APP_METHOD_APPLY_BASKET_BEFORE_SALE = 1;
	public const DISCOUNT_APP_METHOD_APPLY_BASKET_AFTER_SALE = 2;

	public static function createWithDefaultParameters() : self{
		return new self(
			self::randomCode(),
			TebexEffectiveInfo::EFFECTIVE_ON_CART,
			TebexDiscountInfo::DISCOUNT_TYPE_VALUE,
			0,
			0,
			true,
			0,
			true,
			0,
			date("Y-m-d\TH:i:sP"),
			date("Y-m-d\TH:i:sP"),
			self::BASKET_TYPE_BOTH,
			0,
			self::DISCOUNT_APP_METHOD_APPLY_EACH_PACKAGE,
			""
		);
	}

	/** @var string */
	public $code;

	/** @var string */
	public $effective_on;

	/** @var int[] */
	public $packages = [];

	/** @var int[] */
	public $categories = [];

	/** @var string */
	public $discount_type;

	/** @var float */
	public $discount_amount;

	/** @var float */
	public $discount_percentage;

	/** @var bool */
	public $redeem_unlimited;

	/** @var int */
	public $redeem_limit;

	/** @var bool */
	public $expire_never;

	/** @var int */
	public $expire_limit;

	/** @var string */
	public $expire_date; // yyyy-mm-dd

	/** @var string */
	public $start_date;

	/** @var string */
	public $basket_type;

	/** @var float */
	public $minimum;

	/** @var int */
	public $discount_application_method;

	/** @var string */
	public $username = "";

	/** @var string */
	public $note;

	public function __construct(
		string $code,
		string $effective_on,
		string $discount_type,
		float $discount_amount,
		float $discount_percentage,
		bool $redeem_unlimited,
		int $redeem_limit,
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
		$this->redeem_limit = $redeem_limit;
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
			"redeem_limit" => $this->redeem_limit,
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

	/**
	 * Creates a random coupon code based on the Tebex format XXXX-XXXX-XXXX
	 *
	 * @return string
	 */
	public static function randomCode() : string{
		static $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return implode("-", [
			substr(str_shuffle($chars), 0, 4),
			substr(str_shuffle($chars), 0, 4),
			substr(str_shuffle($chars), 0, 4)
		]);
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\sales;

use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\TebexEffectiveInfo;

final class TebexSale{

	/** @var int */
	private $id;

	/** @var TebexEffectiveInfo */
	private $effective;

	/** @var TebexDiscountInfo */
	private $discount;

	/** @var int */
	private $start;

	/** @var int */
	private $expire;

	/** @var int */
	private $order;

	public function __construct(int $id, TebexEffectiveInfo $effective, TebexDiscountInfo $discount, int $start, int $expire, int $order){
		$this->id = $id;
		$this->effective = $effective;
		$this->discount = $discount;
		$this->start = $start;
		$this->expire = $expire;
		$this->order = $order;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getEffective() : TebexEffectiveInfo{
		return $this->effective;
	}

	public function getDiscount() : TebexDiscountInfo{
		return $this->discount;
	}

	public function getStart() : int{
		return $this->start;
	}

	public function getExpire() : int{
		return $this->expire;
	}

	public function getOrder() : int{
		return $this->order;
	}
}
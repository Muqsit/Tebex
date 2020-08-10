<?php

declare(strict_types=1);

namespace muqsit\tebex\api\sales;

final class TebexSale{

	/** @var int */
	private $id;

	/** @var TebexSaleEffectiveInfo */
	private $effective;

	/** @var TebexSaleDiscountInfo */
	private $discount;

	/** @var int */
	private $start;

	/** @var int */
	private $expire;

	/** @var int */
	private $order;

	public function __construct(int $id, TebexSaleEffectiveInfo $effective, TebexSaleDiscountInfo $discount, int $start, int $expire, int $order){
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

	public function getEffective() : TebexSaleEffectiveInfo{
		return $this->effective;
	}

	public function getDiscount() : TebexSaleDiscountInfo{
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
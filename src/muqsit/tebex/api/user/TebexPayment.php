<?php

declare(strict_types=1);

namespace muqsit\tebex\api\user;

final class TebexPayment{

	/** @var string */
	private $transaction_id;

	/** @var int */
	private $time;

	/** @var float */
	private $price;

	/** @var string */
	private $currency;

	/** @var int */
	private $status;

	public function __construct(string $transaction_id, int $time, float $price, string $currency, int $status){
		$this->transaction_id = $transaction_id;
		$this->time = $time;
		$this->price = $price;
		$this->currency = $currency;
		$this->status = $status;
	}

	public function getTransactionId() : string{
		return $this->transaction_id;
	}

	public function getTime() : int{
		return $this->time;
	}

	public function getPrice() : float{
		return $this->price;
	}

	public function getCurrency() : string{
		return $this->currency;
	}

	public function getStatus() : int{
		return $this->status;
	}
}
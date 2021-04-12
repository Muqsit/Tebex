<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\user;

final class TebexPayment{

	private string $transaction_id;
	private int $time;
	private float $price;
	private string $currency;
	private int $status;

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
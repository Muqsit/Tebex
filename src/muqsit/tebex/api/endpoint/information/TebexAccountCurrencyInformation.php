<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\information;

final class TebexAccountCurrencyInformation{

	private string $iso_4217;
	private string $symbol;

	public function __construct(string $iso_4217, string $symbol){
		$this->iso_4217 = $iso_4217;
		$this->symbol = $symbol;
	}

	public function getIso4217() : string{
		return $this->iso_4217;
	}

	public function getSymbol() : string{
		return $this->symbol;
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

final class TebexGUIItem{

	private string $value;

	public function __construct(string $value){
		$this->value = $value;
	}

	public function getValue() : string{
		return $this->value;
	}
}
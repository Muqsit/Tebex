<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

final class TebexCommand{

	private string $string;

	public function __construct(string $string){
		$this->string = $string;
	}

	public function asRawString() : string{
		return $this->string;
	}
}
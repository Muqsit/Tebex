<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

final class EmptyTebexResponse implements TebexResponse{

	public static function instance() : self{
		static $instance = null;
		return $instance ?? $instance = new self();
	}

	private function __construct(){
		// NOOP
	}
}
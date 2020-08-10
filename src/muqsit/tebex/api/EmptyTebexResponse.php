<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

final class EmptyTebexResponse implements TebexResponse{

	public static function instance() : self{
		/**
		 * Do not call this method manually.
		 * This method will automatically be called for a TebexRequest
		 * that does not implement RespondingTebexRequest.
		 */
		static $instance = null;
		return $instance ?? $instance = new self();
	}

	private function __construct(){
		// NOOP
	}
}
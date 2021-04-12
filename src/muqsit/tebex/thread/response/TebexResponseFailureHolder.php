<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

use muqsit\tebex\thread\TebexException;

/**
 * @phpstan-template TTebexResponse of \muqsit\tebex\api\TebexResponse
 * @phpstan-extends TebexResponseHolder<TTebexResponse>
 */
final class TebexResponseFailureHolder extends TebexResponseHolder{

	private TebexException $exception;

	public function __construct(int $handler_id, float $latency, TebexException $exception){
		parent::__construct($handler_id, $latency);
		$this->exception = $exception;
	}

	public function trigger(TebexResponseHandler $handler) : void{
		($handler->on_failure)($this->exception);
	}
}
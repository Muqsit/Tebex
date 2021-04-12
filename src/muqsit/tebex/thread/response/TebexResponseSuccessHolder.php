<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-template TTebexResponse of \muqsit\tebex\api\TebexResponse
 * @phpstan-extends TebexResponseHolder<TTebexResponse>
 */
final class TebexResponseSuccessHolder extends TebexResponseHolder{

	/** @phpstan-var TTebexResponse */
	private TebexResponse $response;

	/**
	 * @param int $handler_id
	 * @param float $latency
	 * @param TebexResponse $response
	 *
	 * @phpstan-param TTebexResponse $response
	 */
	public function __construct(int $handler_id, float $latency, TebexResponse $response){
		parent::__construct($handler_id, $latency);
		$this->response = $response;
	}

	public function trigger(TebexResponseHandler $handler) : void{
		($handler->on_success)($this->response);
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\request;

use muqsit\tebex\api\TebexRequest;
use muqsit\tebex\api\TebexResponse;

final class TebexRequestHolder{

	/** @var int */
	public $handler_id;

	/** @var TebexRequest<TebexResponse> */
	public $request;

	/**
	 * @param TebexRequest<TebexResponse> $request
	 * @param int $handler_id
	 */
	public function __construct(TebexRequest $request, int $handler_id){
		$this->request = $request;
		$this->handler_id = $handler_id;
	}
}
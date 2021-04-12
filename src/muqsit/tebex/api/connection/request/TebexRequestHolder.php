<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection\request;

use muqsit\tebex\api\connection\response\TebexResponse;

final class TebexRequestHolder{

	public int $handler_id;

	/** @phpstan-var TebexRequest<TebexResponse> */
	public TebexRequest $request;

	/**
	 * @param TebexRequest<TebexResponse> $request
	 * @param int $handler_id
	 */
	public function __construct(TebexRequest $request, int $handler_id){
		$this->request = $request;
		$this->handler_id = $handler_id;
	}
}
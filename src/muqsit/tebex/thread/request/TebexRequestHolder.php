<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\request;

use muqsit\tebex\api\TebexRequest;

final class TebexRequestHolder{

	/** @var int */
	public $handler_id;

	/** @var TebexRequest */
	public $request;

	public function __construct(TebexRequest $request, int $handler_id){
		$this->request = $request;
		$this->handler_id = $handler_id;
	}
}
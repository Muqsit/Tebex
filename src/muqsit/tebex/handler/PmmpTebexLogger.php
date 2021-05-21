<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use Logger;
use muqsit\tebexapi\utils\logger\TebexLogger;
use Throwable;

final class PmmpTebexLogger implements TebexLogger{

	private Logger $logger;

	public function __construct(Logger $logger){
		$this->logger = $logger;
	}

	public function exception(Throwable $t) : void{
		$this->logger->logException($t);
	}
}
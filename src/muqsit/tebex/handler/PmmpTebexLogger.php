<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use Logger;
use muqsit\tebexapi\utils\logger\TebexLogger;
use Throwable;

final class PmmpTebexLogger implements TebexLogger{

	public function __construct(
		private Logger $logger
	){}

	public function exception(Throwable $t) : void{
		$this->logger->logException($t);
	}
}
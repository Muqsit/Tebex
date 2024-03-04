<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use Logger;
use muqsit\tebexapi\utils\logger\TebexLogger;
use muqsit\tebexapi\utils\TebexException;
use Throwable;

final class PmmpTebexLogger implements TebexLogger{

	public function __construct(
		readonly private Logger $logger
	){}

	public function exception(Throwable $t) : void{
		$this->logger->logException($t);
		if($t instanceof TebexException && $t->extra_trace !== null){
			echo $t->extra_trace;
		}
	}
}
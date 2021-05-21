<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\logger;

use Throwable;

final class IgnoredTebexLogger implements TebexLogger{

	public function exception(Throwable $t) : void{
		// ignored
	}
}
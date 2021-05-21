<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\logger;

use Throwable;

interface TebexLogger{

	public function exception(Throwable $t) : void;
}
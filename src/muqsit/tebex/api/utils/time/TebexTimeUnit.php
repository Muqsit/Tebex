<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\time;

interface TebexTimeUnit{

	public function getName() : string;

	public function toSeconds(int $value) : int;
}
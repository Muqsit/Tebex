<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\sort;

interface Sortable{

	public function getOrder() : int;
}
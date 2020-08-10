<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils\sort;

final class Sorter{

	/**
	 * @param Sortable[] $sortables
	 */
	public static function sort(array &$sortables) : void{
		uasort($sortables, static function(Sortable $a, Sortable $b) : int{ return $a->getOrder() <=> $b->getOrder(); });
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue;

final class TebexDuePlayersMeta{

	/** @var bool */
	private $execute_offline;

	/** @var int */
	private $next_check;

	/** @var bool */
	private $more;

	public function __construct(bool $execute_offline, int $next_check, bool $more){
		$this->execute_offline = $execute_offline;
		$this->next_check = $next_check;
		$this->more = $more;
	}

	public function shouldExecuteOffline() : bool{
		return $this->execute_offline;
	}

	public function getNextCheck() : int{
		return $this->next_check;
	}

	public function hasMore() : bool{
		return $this->more;
	}
}
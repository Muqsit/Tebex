<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\queue;

final class TebexDuePlayersMeta{

	private bool $execute_offline;
	private int $next_check;
	private bool $more;

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
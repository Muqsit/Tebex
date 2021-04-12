<?php

declare(strict_types=1);

namespace muqsit\tebex\api\information;

use muqsit\tebex\api\TebexResponse;

final class TebexInformation implements TebexResponse{

	private TebexAccountInformation $account;
	private TebexServerInformation $server;

	public function __construct(TebexAccountInformation $account, TebexServerInformation $server){
		$this->account = $account;
		$this->server = $server;
	}

	public function getAccount() : TebexAccountInformation{
		return $this->account;
	}

	public function getServer() : TebexServerInformation{
		return $this->server;
	}
}
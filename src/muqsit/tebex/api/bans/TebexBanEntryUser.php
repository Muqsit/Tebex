<?php

declare(strict_types=1);

namespace muqsit\tebex\api\bans;

final class TebexBanEntryUser{

	/** @var string */
	private $username;

	/** @var string */
	private $uuid;

	public function __construct(string $username, string $uuid){
		$this->username = $username;
		$this->uuid = $uuid;
	}

	public function getUsername() : string{
		return $this->username;
	}

	public function getUuid() : string{
		return $this->uuid;
	}
}
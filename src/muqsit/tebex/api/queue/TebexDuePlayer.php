<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue;

final class TebexDuePlayer{

	/**
	 * @param array<string, mixed> $data
	 * @return self
	 */
	public static function fromTebexResponse(array $data) : self{
		/** @phpstan-var array{id: string|int, name: string, uuid: string} $data */
		return new self((int) $data["id"], $data["name"], $data["uuid"]);
	}

	private int $id;
	private string $name;
	private string $uuid;

	public function __construct(int $id, string $name, string $uuid){
		$this->id = $id;
		$this->name = $name;
		$this->uuid = $uuid;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getUuid() : string{
		return $this->uuid;
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\queue;

final class TebexDuePlayer{

	/**
	 * @param array<string, mixed> $data
	 * @return self
	 */
	public static function fromTebexResponse(array $data) : self{
		return new self($data["id"], $data["name"], $data["uuid"]);
	}

	/** @var int */
	private $id;

	/** @var string */
	private $name;

	/** @var string */
	private $uuid;

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
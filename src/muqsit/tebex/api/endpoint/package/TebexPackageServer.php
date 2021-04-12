<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\package;

final class TebexPackageServer{

	private int $id;
	private string $name;

	public function __construct(int $id, string $name){
		$this->id = $id;
		$this->name = $name;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getName() : string{
		return $this->name;
	}
}
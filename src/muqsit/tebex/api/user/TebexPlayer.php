<?php

declare(strict_types=1);

namespace muqsit\tebex\api\user;

final class TebexPlayer{

	/** @var string */
	private $id;

	/** @var string */
	private $created_at;

	/** @var string */
	private $updated_at;

	/** @var string */
	private $cache_expire;

	/** @var string */
	private $username;

	/** @var string */
	private $meta;

	/** @var int */
	private $plugin_username_id;

	public function __construct(string $id, string $created_at, string $updated_at, string $cache_expire, string $username, string $meta, int $plugin_username_id){
		$this->id = $id;
		$this->created_at = $created_at;
		$this->updated_at = $updated_at;
		$this->cache_expire = $cache_expire;
		$this->username = $username;
		$this->meta = $meta;
		$this->plugin_username_id = $plugin_username_id;
	}

	public function getId() : string{
		return $this->id;
	}

	public function getCreatedAt() : string{
		return $this->created_at;
	}

	public function getUpdatedAt() : string{
		return $this->updated_at;
	}

	public function getCacheExpire() : string{
		return $this->cache_expire;
	}

	public function getUsername() : string{
		return $this->username;
	}

	public function getMeta() : string{
		return $this->meta;
	}

	public function getPluginUsernameId() : int{
		return $this->plugin_username_id;
	}
}
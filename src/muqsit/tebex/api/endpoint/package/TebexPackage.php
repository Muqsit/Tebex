<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\package;

use muqsit\tebex\api\connection\response\TebexResponse;
use muqsit\tebex\api\utils\TebexGuiItem;
use muqsit\tebex\api\utils\time\TebexTime;

final class TebexPackage implements TebexResponse{

	/**
	 * @param array<string, mixed> $data
	 * @return self
	 */
	public static function fromTebexResponse(array $data) : self{
		/**
		 * @phpstan-var array{
		 * 		category: array{id: int, name: string},
		 * 		servers: array{id: int, name: string},
		 * 		id: int,
		 * 		name: string,
		 * 		image: string|false,
		 * 		price: float,
		 * 		expiry_length: int,
		 * 		expiry_period: string,
		 * 		type: string,
		 * 		global_limit: int,
		 * 		global_limit_period: string,
		 * 		user_limit: int,
		 * 		user_limit_period: string,
		 * 		required_packages: int[],
		 * 		require_any: bool,
		 * 		create_giftcard: bool,
		 * 		show_until: int|false,
		 * 		gui_item: string,
		 * 		disabled: bool,
		 * 		disable_quantity: bool,
		 * 		custom_price: bool,
		 * 		choose_server: bool,
		 * 		limit_expires: bool,
		 * 		inherit_commands: bool,
		 * 		variable_giftcard: bool
		 * } $data
		 */

		$servers = [];
		foreach($data["servers"] as ["id" => $id, "name" => $name]){
			$servers[] = new TebexPackageServer($id, $name);
		}

		$category = $data["category"];
		return new self(
			$data["id"],
			$data["name"],
			$data["image"] !== false ? $data["image"] : null,
			$data["price"],
			TebexTime::create($data["expiry_length"], $data["expiry_period"]),
			$data["type"],
			new TebexPackageCategory($category["id"], $category["name"]),
			TebexTime::create($data["global_limit"], $data["global_limit_period"]),
			TebexTime::create($data["user_limit"], $data["user_limit_period"]),
			$servers,
			$data["required_packages"],
			$data["require_any"],
			$data["create_giftcard"],
			$data["show_until"] !== false ? $data["show_until"] : null,
			new TebexGuiItem($data["gui_item"]),
			$data["disabled"],
			$data["disable_quantity"],
			$data["custom_price"],
			$data["choose_server"],
			$data["limit_expires"],
			$data["inherit_commands"],
			$data["variable_giftcard"]
		);
	}

	private int $id;
	private string $name;
	private ?string $image;
	private float $price;
	private TebexTime $expiry;
	private string $type;
	private TebexPackageCategory $category;
	private TebexTime $global_limit;
	private TebexTime $user_limit;

	/** @var TebexPackageServer[] */
	private array $servers;

	/** @var int[] */
	private array $required_package_ids;

	private bool $require_any;
	private bool $create_giftcard;
	private ?int $show_until;
	private TebexGuiItem $gui_item;
	private bool $disabled;
	private bool $disabled_quantity;
	private bool $custom_price;
	private bool $choose_server;
	private bool $limit_expires;
	private bool $inherit_commands;
	private bool $variable_giftcard;

	/**
	 * @param int $id
	 * @param string $name
	 * @param string|null $image
	 * @param float $price
	 * @param TebexTime $expiry
	 * @param string $type
	 * @param TebexPackageCategory $category
	 * @param TebexTime $global_limit
	 * @param TebexTime $user_limit
	 * @param TebexPackageServer[] $servers
	 * @param int[] $required_package_ids
	 * @param bool $require_any
	 * @param bool $create_giftcard
	 * @param int|null $show_until
	 * @param TebexGuiItem $gui_item
	 * @param bool $disabled
	 * @param bool $disabled_quantity
	 * @param bool $custom_price
	 * @param bool $choose_server
	 * @param bool $limit_expires
	 * @param bool $inherit_commands
	 * @param bool $variable_giftcard
	 */
	public function __construct(
		int $id,
		string $name,
		?string $image,
		float $price,
		TebexTime $expiry,
		string $type,
		TebexPackageCategory $category,
		TebexTime $global_limit,
		TebexTime $user_limit,
		array $servers,
		array $required_package_ids,
		bool $require_any,
		bool $create_giftcard,
		?int $show_until,
		TebexGuiItem $gui_item,
		bool $disabled,
		bool $disabled_quantity,
		bool $custom_price,
		bool $choose_server,
		bool $limit_expires,
		bool $inherit_commands,
		bool $variable_giftcard
	){
		$this->id = $id;
		$this->name = $name;
		$this->image = $image;
		$this->price = $price;
		$this->expiry = $expiry;
		$this->type = $type;
		$this->category = $category;
		$this->global_limit = $global_limit;
		$this->user_limit = $user_limit;
		$this->servers = $servers;
		$this->required_package_ids = $required_package_ids;
		$this->require_any = $require_any;
		$this->create_giftcard = $create_giftcard;
		$this->show_until = $show_until;
		$this->gui_item = $gui_item;
		$this->disabled = $disabled;
		$this->disabled_quantity = $disabled_quantity;
		$this->custom_price = $custom_price;
		$this->choose_server = $choose_server;
		$this->limit_expires = $limit_expires;
		$this->inherit_commands = $inherit_commands;
		$this->variable_giftcard = $variable_giftcard;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getImage() : ?string{
		return $this->image;
	}

	public function getPrice() : float{
		return $this->price;
	}

	public function getExpiry() : TebexTime{
		return $this->expiry;
	}

	public function getType() : string{
		return $this->type;
	}

	public function getCategory() : TebexPackageCategory{
		return $this->category;
	}

	public function getGlobalLimit() : TebexTime{
		return $this->global_limit;
	}

	public function getUserLimit() : TebexTime{
		return $this->user_limit;
	}

	/**
	 * @return TebexPackageServer[]
	 */
	public function getServers() : array{
		return $this->servers;
	}

	/**
	 * @return int[]
	 */
	public function getRequiredPackageIds() : array{
		return $this->required_package_ids;
	}

	public function requiresAny() : bool{
		return $this->require_any;
	}

	public function createsGiftcard() : bool{
		return $this->create_giftcard;
	}

	public function getShowUntil() : ?int{
		return $this->show_until;
	}

	public function getGuiItem() : TebexGuiItem{
		return $this->gui_item;
	}

	public function isDisabled() : bool{
		return $this->disabled;
	}

	public function isQuantityDisabled() : bool{
		return $this->disabled_quantity;
	}

	public function hasCustomPrice() : bool{
		return $this->custom_price;
	}

	public function canChooseServer() : bool{
		return $this->choose_server;
	}

	public function limitExpires() : bool{
		return $this->limit_expires;
	}

	public function inheritsCommands() : bool{
		return $this->inherit_commands;
	}

	public function hasVariableGiftcard() : bool{
		return $this->variable_giftcard;
	}
}
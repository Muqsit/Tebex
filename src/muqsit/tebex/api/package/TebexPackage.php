<?php

declare(strict_types=1);

namespace muqsit\tebex\api\package;

use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\utils\TebexGUIItem;
use muqsit\tebex\api\utils\time\TebexTime;
use pocketmine\item\Item;

final class TebexPackage implements TebexResponse{

	/**
	 * @param array<string, mixed> $data
	 * @return self
	 */
	public static function fromTebexResponse(array $data) : self{
		$category = $data["category"];

		$servers = [];
		foreach($data["servers"] as ["id" => $id, "name" => $name]){
			$servers[] = new TebexPackageServer($id, $name);
		}

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
			new TebexGUIItem($data["gui_item"]),
			$data["disabled"],
			$data["disable_quantity"],
			$data["custom_price"],
			$data["choose_server"],
			$data["limit_expires"],
			$data["inherit_commands"],
			$data["variable_giftcard"]
		);
	}

	/** @var int */
	private $id;

	/** @var string */
	private $name;

	/** @var string|null */
	private $image;

	/** @var float */
	private $price;

	/** @var TebexTime */
	private $expiry;

	/** @var string */
	private $type;

	/** @var TebexPackageCategory */
	private $category;

	/** @var TebexTime */
	private $global_limit;

	/** @var TebexTime */
	private $user_limit;

	/** @var TebexPackageServer[] */
	private $servers;

	/** @var int[] */
	private $required_package_ids;

	/** @var bool */
	private $require_any;

	/** @var bool */
	private $create_giftcard;

	/** @var int|null */
	private $show_until;

	/** @var TebexGUIItem */
	private $gui_item;

	/** @var bool */
	private $disabled;

	/** @var bool */
	private $disabled_quantity;

	/** @var bool */
	private $custom_price;

	/** @var bool */
	private $choose_server;

	/** @var bool */
	private $limit_expires;

	/** @var bool */
	private $inherit_commands;

	/** @var bool */
	private $variable_giftcard;

	/**
	 * TebexPackage constructor.
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
	 * @param TebexGUIItem $gui_item
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
		TebexGUIItem $gui_item,
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

	public function getGuiItem() : ?Item{
		return $this->gui_item->asItem();
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
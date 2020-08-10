<?php

declare(strict_types=1);

namespace muqsit\tebex\api\listing;

use pocketmine\item\Item;
use muqsit\tebex\api\utils\sort\Sortable;
use muqsit\tebex\api\utils\TebexGUIItem;

abstract class BaseTebexCategory implements Sortable{

	/** @var int */
	private $id;

	/** @var int */
	private $order;

	/** @var string */
	private $name;

	/** @var TebexPackage[] */
	private $packages;

	/** @var TebexGUIItem */
	private $gui_item;

	/**
	 * @param int $id
	 * @param int $order
	 * @param string $name
	 * @param TebexPackage[] $packages
	 * @param TebexGUIItem $gui_item
	 */
	public function __construct(int $id, int $order, string $name, array $packages, TebexGUIItem $gui_item){
		$this->id = $id;
		$this->order = $order;
		$this->name = $name;
		$this->packages = $packages;
		$this->gui_item = $gui_item;
	}

	final public function getId() : int{
		return $this->id;
	}

	final public function getOrder() : int{
		return $this->order;
	}

	final public function getName() : string{
		return $this->name;
	}

	/**
	 * @return TebexPackage[]
	 */
	final public function getPackages() : array{
		return $this->packages;
	}

	final public function getGuiItem() : ?Item{
		return $this->gui_item->asItem();
	}
}
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\listing;

use muqsit\tebex\api\utils\sort\Sortable;
use muqsit\tebex\api\utils\TebexGuiItem;

abstract class BaseTebexCategory implements Sortable{

	private int $id;
	private int $order;
	private string $name;

	/** @var TebexPackage[] */
	private array $packages;

	private TebexGuiItem $gui_item;

	/**
	 * @param int $id
	 * @param int $order
	 * @param string $name
	 * @param TebexPackage[] $packages
	 * @param TebexGuiItem $gui_item
	 */
	public function __construct(int $id, int $order, string $name, array $packages, TebexGuiItem $gui_item){
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

	final public function getGuiItem() : TebexGuiItem{
		return $this->gui_item;
	}
}
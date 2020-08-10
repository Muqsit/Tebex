<?php

declare(strict_types=1);

namespace muqsit\tebex\api\listing;

use muqsit\tebex\api\utils\TebexGUIItem;

final class TebexCategory extends BaseTebexCategory{

	/** @var bool */
	private $only_subcategories;

	/** @var TebexSubCategory[] */
	private $subcategories;

	/**
	 * @param int $id
	 * @param int $order
	 * @param string $name
	 * @param TebexPackage[] $packages
	 * @param TebexGUIItem $gui_item
	 * @param bool $only_subcategories
	 * @param TebexSubCategory[] $subcategories
	 */
	public function __construct(int $id, int $order, string $name, array $packages, TebexGUIItem $gui_item, bool $only_subcategories, array $subcategories){
		parent::__construct($id, $order, $name, $packages, $gui_item);
		$this->only_subcategories = $only_subcategories;
		$this->subcategories = $subcategories;
	}

	public function hasOnlySubcategories() : bool{
		return $this->only_subcategories;
	}

	/**
	 * @return TebexSubCategory[]
	 */
	public function getSubcategories() : array{
		return $this->subcategories;
	}
}
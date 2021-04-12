<?php

declare(strict_types=1);

namespace muqsit\tebex\api\listing;

use muqsit\tebex\api\utils\sort\Sortable;
use muqsit\tebex\api\utils\TebexGUIItem;
use pocketmine\item\Item;

class TebexPackage implements Sortable{

	/**
	 * @param array<string, mixed> $response
	 * @return static
	 *
	 * @phpstan-param array{
	 * 		id: int,
	 * 		order: int,
	 * 		name: string,
	 * 		price: string,
	 * 		sale: array{active: bool, discount: string},
	 * 		image: string|false,
	 * 		gui_item: string|int
	 * } $response
	 */
	public static function fromTebexData(array $response) : self{
		return new static(
			$response["id"],
			$response["order"],
			$response["name"],
			$response["price"],
			new TebexPackageSaleInfo(
				$response["sale"]["active"],
				(string) $response["sale"]["discount"]
			),
			$response["image"] !== false ? $response["image"] : null,
			new TebexGUIItem((string) $response["gui_item"])
		);
	}

	private int $id;
	private int $order;
	private string $name;
	private string $price;
	private TebexPackageSaleInfo $sale;
	private ?string $image;
	private TebexGUIItem $gui_item;

	final public function __construct(int $id, int $order, string $name, string $price, TebexPackageSaleInfo $sale, ?string $image, TebexGUIItem $gui_item){
		$this->id = $id;
		$this->order = $order;
		$this->name = $name;
		$this->price = $price;
		$this->sale = $sale;
		$this->image = $image;
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

	final public function getPrice() : string{
		return $this->price;
	}

	final public function getSale() : TebexPackageSaleInfo{
		return $this->sale;
	}

	final public function getImage() : ?string{
		return $this->image;
	}

	final public function getGuiItem() : ?Item{
		return $this->gui_item->asItem();
	}
}
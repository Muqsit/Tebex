<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\listing;

use muqsit\tebex\api\utils\sort\Sortable;
use muqsit\tebex\api\utils\TebexGuiItem;

final class TebexPackage implements Sortable{

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
		return new self(
			$response["id"],
			$response["order"],
			$response["name"],
			$response["price"],
			new TebexPackageSaleInfo(
				$response["sale"]["active"],
				(string) $response["sale"]["discount"]
			),
			$response["image"] !== false ? $response["image"] : null,
			new TebexGuiItem((string) $response["gui_item"])
		);
	}

	private int $id;
	private int $order;
	private string $name;
	private string $price;
	private TebexPackageSaleInfo $sale;
	private ?string $image;
	private TebexGuiItem $gui_item;

	public function __construct(int $id, int $order, string $name, string $price, TebexPackageSaleInfo $sale, ?string $image, TebexGuiItem $gui_item){
		$this->id = $id;
		$this->order = $order;
		$this->name = $name;
		$this->price = $price;
		$this->sale = $sale;
		$this->image = $image;
		$this->gui_item = $gui_item;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getOrder() : int{
		return $this->order;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getPrice() : string{
		return $this->price;
	}

	public function getSale() : TebexPackageSaleInfo{
		return $this->sale;
	}

	public function getImage() : ?string{
		return $this->image;
	}

	public function getGuiItem() : TebexGuiItem{
		return $this->gui_item;
	}
}
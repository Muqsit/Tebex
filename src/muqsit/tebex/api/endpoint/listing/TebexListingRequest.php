<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\listing;

use muqsit\tebex\api\connection\request\TebexGetRequest;
use muqsit\tebex\api\connection\response\TebexResponse;
use muqsit\tebex\api\utils\TebexGuiItem;

/**
 * @phpstan-extends TebexGetRequest<TebexListingInfo>
 */
final class TebexListingRequest extends TebexGetRequest{

	public function getEndpoint() : string{
		return "/listing";
	}


	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{
	 * 		categories: array<array{
	 * 			packages: array,
	 * 			subcategories: array<array{packages: array, id: int, order: int, name: string, gui_item: string|int}>,
	 * 			id: int,
	 * 			order: int,
	 * 			name: string,
	 * 			gui_item: string|int,
	 * 			only_subcategories: bool
	 * 		}>
	 * } $response
	 */
	public function createResponse(array $response) : TebexResponse{
		$categories = [];
		foreach($response["categories"] as $entry){
			$packages = [];
			foreach($entry["packages"] as $package){
				$packages[] = TebexPackage::fromTebexData($package);
			}

			$subcategories = [];
			foreach($entry["subcategories"] as $subcategory){
				$subcategory_packages = [];
				foreach($subcategory["packages"] as $package){
					$subcategory_packages[] = TebexPackage::fromTebexData($package);
				}

				$subcategories[] = new TebexSubCategory(
					$subcategory["id"],
					$subcategory["order"],
					$subcategory["name"],
					$subcategory_packages,
					new TebexGuiItem((string) $subcategory["gui_item"])
				);
			}

			$categories[] = new TebexCategory(
				$entry["id"],
				$entry["order"],
				$entry["name"],
				$packages,
				new TebexGuiItem((string) $entry["gui_item"]),
				$entry["only_subcategories"],
				$subcategories
			);
		}

		return new TebexListingInfo($categories);
	}
}
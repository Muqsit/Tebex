<?php

declare(strict_types=1);

namespace muqsit\tebex\api\endpoint\sales;

use muqsit\tebex\api\connection\request\TebexGetRequest;
use muqsit\tebex\api\connection\response\TebexResponse;
use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\TebexEffectiveInfo;

/**
 * @phpstan-extends TebexGetRequest<TebexSalesList>
 */
final class TebexSalesRequest extends TebexGetRequest{

	public function getEndpoint() : string{
		return "/sales";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	/**
	 * @param array<string, mixed> $response
	 * @return TebexResponse
	 *
	 * @phpstan-param array{
	 * 		data: array<array{
	 * 			id: int,
	 * 			effective: array{type: string, packages: int[], categories: int[]},
	 * 			discount: array{type: string, percentage: int, value: int},
	 * 			start: int,
	 * 			expire: int,
	 * 			order: int
	 * 		}>
	 * } $response
	 */
	public function createResponse(array $response) : TebexResponse{
		$sales = [];
		foreach($response["data"] as [
			"id" => $id,
			"effective" => $effective,
			"discount" => $discount,
			"start" => $start,
			"expire" => $expire,
			"order" => $porder
		]){
			$sales[] = new TebexSale(
				$id,
				TebexEffectiveInfo::fromTebexResponse($effective),
				TebexDiscountInfo::fromTebexResponse($discount),
				$start,
				$expire,
				$porder
			);
		}
		return new TebexSalesList($sales);
	}
}
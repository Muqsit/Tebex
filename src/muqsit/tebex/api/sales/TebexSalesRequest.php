<?php

declare(strict_types=1);

namespace muqsit\tebex\api\sales;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\api\RespondingTebexRequest;

final class TebexSalesRequest extends TebexGETRequest implements RespondingTebexRequest{

	public function getEndpoint() : string{
		return "/sales";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

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
				new TebexSaleEffectiveInfo(
					$effective["type"],
					$effective["packages"],
					$effective["categories"]
				),
				new TebexSaleDiscountInfo(
					$discount["type"],
					$discount["percentage"],
					$discount["value"]
				),
				$start,
				$expire,
				$porder
			);
		}
		return new TebexSalesList($sales);
	}
}
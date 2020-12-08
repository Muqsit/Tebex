<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\coupon;

use Closure;
use muqsit\tebex\api\coupons\create\TebexCouponCreateResponse;
use muqsit\tebex\api\coupons\create\TebexCreatedCoupon;
use muqsit\tebex\api\coupons\TebexCoupon;
use muqsit\tebex\api\coupons\TebexCouponsList;
use muqsit\tebex\api\coupons\TebexCouponsListPagination;
use muqsit\tebex\Loader;
use muqsit\tebex\thread\response\TebexResponseHandler;
use muqsit\tebex\thread\TebexException;

final class TebexCouponHandler{

	/** @var Loader */
	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * Returns a list of coupons on the specified page.
	 *
	 * @param int $page
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCouponsListPagination, TebexCoupon[]) : void $callback
	 */
	public function getPage(int $page, Closure $callback) : void{
		$this->plugin->getApi()->getCoupons($page, TebexResponseHandler::onSuccess(static function(TebexCouponsList $list) use($callback) : void{
			$callback($list->getPagination(), $list->getCoupons());
		}));
	}

	/**
	 * Returns all coupons.
	 * You may want to use {@see TebexCouponHandler::getAllSegmented()} instead, which
	 * is a lot less memory intensive.
	 *
	 * @param int $_internal_page_pointer
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCouponsListPagination, TebexCoupon[]) : void $callback
	 */
	public function getAll(Closure $callback, int $_internal_page_pointer = 1) : void{
		$coupons = [];
		$this->getPage($_internal_page_pointer, function(TebexCouponsListPagination $pagination, array $coupons_on_page) use($_internal_page_pointer, $callback, &$coupons) : void{
			array_push($coupons, ...$coupons_on_page);
			if($pagination->getNextUrl() !== null){
				$this->getAll($callback, $_internal_page_pointer + 1);
			}else{
				$callback($pagination, $coupons);
			}
		});
	}

	/**
	 * Returns all coupons in segments, each segment calls the callback which may
	 * return true to continue traversing further, or false to stop traversing.
	 *
	 * @param int $_internal_page_pointer
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCouponsListPagination, TebexCoupon[]) : bool $callback
	 */
	public function getAllSegmented(Closure $callback, int $_internal_page_pointer = 1) : void{
		$this->getPage($_internal_page_pointer, function(TebexCouponsListPagination $pagination, array $coupons_on_page) use($_internal_page_pointer, $callback) : void{
			if($callback($pagination, $coupons_on_page) && $pagination->getNextUrl() !== null){
				$this->getAllSegmented($callback, $_internal_page_pointer + 1);
			}
		});
	}

	/**
	 * Returns the coupon of the given coupon ID and returns it if it exists,
	 * null or else.
	 * Coupon ID differs from coupon CODE. To get a coupon of the given code,
	 * use {@see TebexCouponHandler::search()}.
	 *
	 * @param int $id
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCoupon|null) : void $callback
	 */
	public function get(int $id, Closure $callback) : void{
		$this->plugin->getApi()->getCoupon($id, new TebexResponseHandler($callback, function(TebexException $_) use($callback) : void{ $callback(null); }));
	}

	/**
	 * Searches for the coupon of the given code and returns it if it exists,
	 * null or else.
	 *
	 * @param string $code
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCoupon|null) : void $callback
	 */
	public function search(string $code, Closure $callback) : void{
		$this->getAllSegmented(function(TebexCouponsListPagination $pagination, array $coupons) use($code, $callback) : bool{
			/** @var TebexCoupon $coupon */
			foreach($coupons as $coupon){
				if($coupon->getCode() === $code){
					$callback($coupon);
					return false;
				}
			}
			if($pagination->getNextUrl() === null){
				$callback(null);
			}
			return true;
		});
	}

	/**
	 * @param TebexCreatedCoupon $coupon
	 * @param Closure $callback
	 *
	 * @phpstan-param Closure(TebexCoupon) : void $callback
	 */
	public function create(TebexCreatedCoupon $coupon, Closure $callback) : void{
		$this->plugin->getApi()->createCoupon($coupon, TebexResponseHandler::onSuccess(function(TebexCouponCreateResponse $response) use($callback) : void{
			$this->get($response->id, function(?TebexCoupon $coupon) use($callback) : void{
				if($coupon !== null){
					$callback($coupon);
				}
			});
		}));
	}

	public function delete(int $id) : void{
		$this->plugin->getApi()->deleteCoupon($id);
	}
}
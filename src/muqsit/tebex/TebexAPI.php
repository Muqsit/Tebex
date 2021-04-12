<?php

declare(strict_types=1);

namespace muqsit\tebex;

use muqsit\tebex\api\connection\response\EmptyTebexResponse;
use muqsit\tebex\api\connection\response\TebexResponseHandler;
use muqsit\tebex\api\endpoint\bans\TebexBanEntry;
use muqsit\tebex\api\endpoint\bans\TebexBanList;
use muqsit\tebex\api\endpoint\bans\TebexBanListRequest;
use muqsit\tebex\api\endpoint\bans\TebexBanRequest;
use muqsit\tebex\api\endpoint\checkout\TebexCheckoutInfo;
use muqsit\tebex\api\endpoint\checkout\TebexCheckoutRequest;
use muqsit\tebex\api\endpoint\coupons\create\TebexCouponCreateRequest;
use muqsit\tebex\api\endpoint\coupons\create\TebexCouponCreateResponse;
use muqsit\tebex\api\endpoint\coupons\create\TebexCreatedCoupon;
use muqsit\tebex\api\endpoint\coupons\TebexCoupon;
use muqsit\tebex\api\endpoint\coupons\TebexCouponRequest;
use muqsit\tebex\api\endpoint\coupons\TebexCouponsList;
use muqsit\tebex\api\endpoint\coupons\TebexCouponsRequest;
use muqsit\tebex\api\endpoint\information\TebexInformation;
use muqsit\tebex\api\endpoint\information\TebexInformationRequest;
use muqsit\tebex\api\endpoint\listing\TebexListingInfo;
use muqsit\tebex\api\endpoint\listing\TebexListingRequest;
use muqsit\tebex\api\endpoint\package\TebexPackage;
use muqsit\tebex\api\endpoint\package\TebexPackageRequest;
use muqsit\tebex\api\endpoint\package\TebexPackages;
use muqsit\tebex\api\endpoint\package\TebexPackagesRequest;
use muqsit\tebex\api\endpoint\queue\commands\offline\TebexQueuedOfflineCommandsInfo;
use muqsit\tebex\api\endpoint\queue\commands\offline\TebexQueuedOfflineCommandsListRequest;
use muqsit\tebex\api\endpoint\queue\commands\online\TebexQueuedOnlineCommandsInfo;
use muqsit\tebex\api\endpoint\queue\commands\online\TebexQueuedOnlineCommandsListRequest;
use muqsit\tebex\api\endpoint\queue\commands\TebexDeleteCommandRequest;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayersInfo;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayersListRequest;
use muqsit\tebex\api\endpoint\sales\TebexSalesList;
use muqsit\tebex\api\endpoint\sales\TebexSalesRequest;
use muqsit\tebex\api\endpoint\user\TebexUser;
use muqsit\tebex\api\endpoint\user\TebexUserLookupRequest;

final class TebexAPI extends BaseTebexAPI{

	/**
	 * @param TebexResponseHandler<TebexInformation> $callback
	 */
	public function getInformation(TebexResponseHandler $callback) : void{
		$this->request(new TebexInformationRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexSalesList> $callback
	 */
	public function getSales(TebexResponseHandler $callback) : void{
		$this->request(new TebexSalesRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexCouponsList> $callback
	 */
	public function getCoupons(TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponsRequest(), $callback);
	}

	/**
	 * @param int $coupon_id
	 * @param TebexResponseHandler<TebexCoupon> $callback
	 */
	public function getCoupon(int $coupon_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponRequest($coupon_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexBanList> $callback
	 */
	public function getBanList(TebexResponseHandler $callback) : void{
		$this->request(new TebexBanListRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexQueuedOfflineCommandsInfo> $callback
	 */
	public function getQueuedOfflineCommands(TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOfflineCommandsListRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexDuePlayersInfo> $callback
	 */
	public function getDuePlayersList(TebexResponseHandler $callback) : void{
		$this->request(new TebexDuePlayersListRequest(), $callback);
	}

	/**
	 * @param int $player_id
	 * @param TebexResponseHandler<TebexQueuedOnlineCommandsInfo> $callback
	 */
	public function getQueuedOnlineCommands(int $player_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOnlineCommandsListRequest($player_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexListingInfo> $callback
	 */
	public function getListing(TebexResponseHandler $callback) : void{
		$this->request(new TebexListingRequest(), $callback);
	}

	/**
	 * @param int $package_id
	 * @param TebexResponseHandler<TebexPackage> $callback
	 */
	public function getPackage(int $package_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexPackageRequest($package_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexPackages> $callback
	 */
	public function getPackages(TebexResponseHandler $callback) : void{
		$this->request(new TebexPackagesRequest(), $callback);
	}

	/**
	 * @param int[] $command_ids
	 * @param TebexResponseHandler<EmptyTebexResponse>|null $callback
	 */
	public function deleteCommands(array $command_ids, ?TebexResponseHandler $callback = null) : void{
		$this->request(new TebexDeleteCommandRequest($command_ids), $callback ?? TebexResponseHandler::onSuccess(static function(EmptyTebexResponse $response) : void{}));
	}

	/**
	 * @param string $username_or_uuid
	 * @param TebexResponseHandler<TebexUser> $callback
	 */
	public function lookup(string $username_or_uuid, TebexResponseHandler $callback) : void{
		$this->request(new TebexUserLookupRequest($username_or_uuid), $callback);
	}

	/**
	 * @param string $username_or_uuid
	 * @param string|null $reason
	 * @param string|null $ip
	 * @param TebexResponseHandler<TebexBanEntry>|null $callback
	 */
	public function ban(string $username_or_uuid, ?string $reason = null, ?string $ip = null, ?TebexResponseHandler $callback = null) : void{ // TODO: test this
		$this->request(new TebexBanRequest($username_or_uuid, $reason, $ip), $callback ?? TebexResponseHandler::onSuccess(static function(TebexBanEntry $response) : void{}));
	}

	/**
	 * @param int $package_id
	 * @param string $username
	 * @param TebexResponseHandler<TebexCheckoutInfo> $callback
	 */
	public function checkout(int $package_id, string $username, TebexResponseHandler $callback) : void{
		$this->request(new TebexCheckoutRequest($package_id, $username), $callback);
	}

	/**
	 * @param TebexCreatedCoupon $coupon
	 * @param TebexResponseHandler<TebexCouponCreateResponse> $callback
	 */
	public function createCoupon(TebexCreatedCoupon $coupon, TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponCreateRequest($coupon), $callback);
	}
}
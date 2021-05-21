<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

use muqsit\tebex\api\connection\request\TebexRequest;
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

abstract class BaseTebexApi implements TebexApi{

	/**
	 * @param TebexRequest $request
	 * @param TebexResponseHandler $callback
	 *
	 * @phpstan-template TTebexResponse of \muqsit\tebex\api\connection\response\TebexResponse
	 * @phpstan-param TebexRequest<TTebexResponse> $request
	 * @phpstan-param TebexResponseHandler<TTebexResponse> $callback
	 */
	abstract public function request(TebexRequest $request, TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexInformation> $callback
	 */
	final public function getInformation(TebexResponseHandler $callback) : void{
		$this->request(new TebexInformationRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexSalesList> $callback
	 */
	final public function getSales(TebexResponseHandler $callback) : void{
		$this->request(new TebexSalesRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexCouponsList> $callback
	 */
	final public function getCoupons(TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponsRequest(), $callback);
	}

	/**
	 * @param int $coupon_id
	 * @param TebexResponseHandler<TebexCoupon> $callback
	 */
	final public function getCoupon(int $coupon_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponRequest($coupon_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexBanList> $callback
	 */
	final public function getBanList(TebexResponseHandler $callback) : void{
		$this->request(new TebexBanListRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexQueuedOfflineCommandsInfo> $callback
	 */
	final public function getQueuedOfflineCommands(TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOfflineCommandsListRequest(), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexDuePlayersInfo> $callback
	 */
	final public function getDuePlayersList(TebexResponseHandler $callback) : void{
		$this->request(new TebexDuePlayersListRequest(), $callback);
	}

	/**
	 * @param int $player_id
	 * @param TebexResponseHandler<TebexQueuedOnlineCommandsInfo> $callback
	 */
	final public function getQueuedOnlineCommands(int $player_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOnlineCommandsListRequest($player_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexListingInfo> $callback
	 */
	final public function getListing(TebexResponseHandler $callback) : void{
		$this->request(new TebexListingRequest(), $callback);
	}

	/**
	 * @param int $package_id
	 * @param TebexResponseHandler<TebexPackage> $callback
	 */
	final public function getPackage(int $package_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexPackageRequest($package_id), $callback);
	}

	/**
	 * @param TebexResponseHandler<TebexPackages> $callback
	 */
	final public function getPackages(TebexResponseHandler $callback) : void{
		$this->request(new TebexPackagesRequest(), $callback);
	}

	/**
	 * @param int[] $command_ids
	 * @param TebexResponseHandler<EmptyTebexResponse>|null $callback
	 */
	final public function deleteCommands(array $command_ids, ?TebexResponseHandler $callback = null) : void{
		$this->request(new TebexDeleteCommandRequest($command_ids), $callback ?? TebexResponseHandler::onSuccess(static function(EmptyTebexResponse $response) : void{}));
	}

	/**
	 * @param string $username_or_uuid
	 * @param TebexResponseHandler<TebexUser> $callback
	 */
	final public function lookup(string $username_or_uuid, TebexResponseHandler $callback) : void{
		$this->request(new TebexUserLookupRequest($username_or_uuid), $callback);
	}

	/**
	 * @param string $username_or_uuid
	 * @param string|null $reason
	 * @param string|null $ip
	 * @param TebexResponseHandler<TebexBanEntry>|null $callback
	 */
	final public function ban(string $username_or_uuid, ?string $reason = null, ?string $ip = null, ?TebexResponseHandler $callback = null) : void{
		$this->request(new TebexBanRequest($username_or_uuid, $reason, $ip), $callback ?? TebexResponseHandler::onSuccess(static function(TebexBanEntry $response) : void{}));
	}

	/**
	 * @param int $package_id
	 * @param string $username
	 * @param TebexResponseHandler<TebexCheckoutInfo> $callback
	 */
	final public function checkout(int $package_id, string $username, TebexResponseHandler $callback) : void{
		$this->request(new TebexCheckoutRequest($package_id, $username), $callback);
	}

	/**
	 * @param TebexCreatedCoupon $coupon
	 * @param TebexResponseHandler<TebexCouponCreateResponse> $callback
	 */
	final public function createCoupon(TebexCreatedCoupon $coupon, TebexResponseHandler $callback) : void{
		$this->request(new TebexCouponCreateRequest($coupon), $callback);
	}
}
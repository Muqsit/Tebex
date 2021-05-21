<?php

declare(strict_types=1);

namespace muqsit\tebex\api;

use muqsit\tebex\api\connection\response\EmptyTebexResponse;
use muqsit\tebex\api\connection\response\TebexResponseHandler;
use muqsit\tebex\api\endpoint\bans\TebexBanEntry;
use muqsit\tebex\api\endpoint\bans\TebexBanList;
use muqsit\tebex\api\endpoint\checkout\TebexCheckoutInfo;
use muqsit\tebex\api\endpoint\coupons\create\TebexCouponCreateResponse;
use muqsit\tebex\api\endpoint\coupons\create\TebexCreatedCoupon;
use muqsit\tebex\api\endpoint\coupons\TebexCoupon;
use muqsit\tebex\api\endpoint\coupons\TebexCouponsList;
use muqsit\tebex\api\endpoint\information\TebexInformation;
use muqsit\tebex\api\endpoint\listing\TebexListingInfo;
use muqsit\tebex\api\endpoint\package\TebexPackage;
use muqsit\tebex\api\endpoint\package\TebexPackages;
use muqsit\tebex\api\endpoint\queue\commands\offline\TebexQueuedOfflineCommandsInfo;
use muqsit\tebex\api\endpoint\queue\commands\online\TebexQueuedOnlineCommandsInfo;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayersInfo;
use muqsit\tebex\api\endpoint\sales\TebexSalesList;
use muqsit\tebex\api\endpoint\user\TebexUser;

interface TebexApi{

	public function getLatency() : float;

	/**
	 * @param TebexResponseHandler<TebexInformation> $callback
	 */
	public function getInformation(TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexSalesList> $callback
	 */
	public function getSales(TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexCouponsList> $callback
	 */
	public function getCoupons(TebexResponseHandler $callback) : void;

	/**
	 * @param int $coupon_id
	 * @param TebexResponseHandler<TebexCoupon> $callback
	 */
	public function getCoupon(int $coupon_id, TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexBanList> $callback
	 */
	public function getBanList(TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexQueuedOfflineCommandsInfo> $callback
	 */
	public function getQueuedOfflineCommands(TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexDuePlayersInfo> $callback
	 */
	public function getDuePlayersList(TebexResponseHandler $callback) : void;

	/**
	 * @param int $player_id
	 * @param TebexResponseHandler<TebexQueuedOnlineCommandsInfo> $callback
	 */
	public function getQueuedOnlineCommands(int $player_id, TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexListingInfo> $callback
	 */
	public function getListing(TebexResponseHandler $callback) : void;

	/**
	 * @param int $package_id
	 * @param TebexResponseHandler<TebexPackage> $callback
	 */
	public function getPackage(int $package_id, TebexResponseHandler $callback) : void;

	/**
	 * @param TebexResponseHandler<TebexPackages> $callback
	 */
	public function getPackages(TebexResponseHandler $callback) : void;

	/**
	 * @param int[] $command_ids
	 * @param TebexResponseHandler<EmptyTebexResponse>|null $callback
	 */
	public function deleteCommands(array $command_ids, ?TebexResponseHandler $callback = null) : void;

	/**
	 * @param string $username_or_uuid
	 * @param TebexResponseHandler<TebexUser> $callback
	 */
	public function lookup(string $username_or_uuid, TebexResponseHandler $callback) : void;

	/**
	 * @param string $username_or_uuid
	 * @param string|null $reason
	 * @param string|null $ip
	 * @param TebexResponseHandler<TebexBanEntry>|null $callback
	 */
	public function ban(string $username_or_uuid, ?string $reason = null, ?string $ip = null, ?TebexResponseHandler $callback = null) : void;

	/**
	 * @param int $package_id
	 * @param string $username
	 * @param TebexResponseHandler<TebexCheckoutInfo> $callback
	 */
	public function checkout(int $package_id, string $username, TebexResponseHandler $callback) : void;

	/**
	 * @param TebexCreatedCoupon $coupon
	 * @param TebexResponseHandler<TebexCouponCreateResponse> $callback
	 */
	public function createCoupon(TebexCreatedCoupon $coupon, TebexResponseHandler $callback) : void;
}
<?php

declare(strict_types=1);

namespace muqsit\tebex;

use muqsit\tebex\api\bans\TebexBanListRequest;
use muqsit\tebex\api\bans\TebexBanRequest;
use muqsit\tebex\api\checkout\TebexCheckoutRequest;
use muqsit\tebex\api\information\TebexInformationRequest;
use muqsit\tebex\api\listing\TebexListingRequest;
use muqsit\tebex\api\package\TebexPackageRequest;
use muqsit\tebex\api\package\TebexPackagesRequest;
use muqsit\tebex\api\queue\TebexDuePlayersListRequest;
use muqsit\tebex\api\queue\commands\TebexDeleteCommandRequest;
use muqsit\tebex\api\queue\commands\offline\TebexQueuedOfflineCommandsListRequest;
use muqsit\tebex\api\queue\commands\online\TebexQueuedOnlineCommandsListRequest;
use muqsit\tebex\api\sales\TebexSalesRequest;
use muqsit\tebex\api\user\TebexUserLookupRequest;
use muqsit\tebex\thread\response\TebexResponseHandler;

final class TebexAPI extends BaseTebexAPI{

	public function getInformation(TebexResponseHandler $callback) : void{
		$this->request(new TebexInformationRequest(), $callback);
	}

	public function getSales(TebexResponseHandler $callback) : void{
		$this->request(new TebexSalesRequest(), $callback);
	}

	public function getBanList(TebexResponseHandler $callback) : void{
		$this->request(new TebexBanListRequest(), $callback);
	}

	public function getQueuedOfflineCommands(TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOfflineCommandsListRequest(), $callback);
	}

	public function getDuePlayersList(TebexResponseHandler $callback) : void{
		$this->request(new TebexDuePlayersListRequest(), $callback);
	}

	public function getQueuedOnlineCommands(int $player_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexQueuedOnlineCommandsListRequest($player_id), $callback);
	}

	public function getListing(TebexResponseHandler $callback) : void{
		$this->request(new TebexListingRequest(), $callback);
	}

	public function getPackage(int $package_id, TebexResponseHandler $callback) : void{
		$this->request(new TebexPackageRequest($package_id), $callback);
	}

	public function getPackages(TebexResponseHandler $callback) : void{
		$this->request(new TebexPackagesRequest(), $callback);
	}

	/**
	 * @param int[] $command_ids
	 * @param TebexResponseHandler|null $callback
	 */
	public function deleteCommands(array $command_ids, ?TebexResponseHandler $callback = null) : void{
		$this->request(new TebexDeleteCommandRequest($command_ids), $callback ?? TebexResponseHandler::unhandled());
	}

	public function lookup(string $username_or_uuid, TebexResponseHandler $callback) : void{
		$this->request(new TebexUserLookupRequest($username_or_uuid), $callback);
	}

	public function ban(string $username_or_uuid, ?string $reason = null, ?string $ip = null, ?TebexResponseHandler $callback = null) : void{ // TODO: test this
		$this->request(new TebexBanRequest($username_or_uuid, $reason, $ip), $callback ?? TebexResponseHandler::unhandled());
	}

	public function checkout(int $package_id, string $username, TebexResponseHandler $callback) : void{
		$this->request(new TebexCheckoutRequest($package_id, $username), $callback);
	}
}
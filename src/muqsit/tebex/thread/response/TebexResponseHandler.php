<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

use Logger;
use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\Loader;
use muqsit\tebex\thread\TebexException;
use Closure;
use pocketmine\Server;

/**
 * @phpstan-template TTebexResponse of TebexResponse
 */
final class TebexResponseHandler{

	/**
	 * @param string $expected_response_class
	 * @return TebexResponseHandler
	 *
	 * @phpstan-template UTebexResponse of TebexResponse
	 * @phpstan-param class-string<UTebexResponse> $expected_response_class
	 * @phpstan-return TebexResponseHandler<UTebexResponse>
	 */
	public static function debug(string $expected_response_class = TebexResponse::class) : self{
		/** @phpstan-var Closure(UTebexResponse) : void $on_success */
		$on_success = Closure::fromCallable("var_dump");
		return self::onSuccess($on_success);
	}

	private static function getLogger() : Logger{
		static $logger = null;
		if($logger === null){
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("Tebex");
			if($plugin instanceof Loader){
				$logger = $plugin->getLogger();
			}
		}
		return $logger;
	}

	/**
	 * @param Closure $on_success
	 * @return TebexResponseHandler
	 *
	 * @phpstan-template UTebexResponse of TebexResponse
	 * @phpstan-param Closure(UTebexResponse) : void $on_success
	 * @phpstan-return TebexResponseHandler<UTebexResponse>
	 */
	public static function onSuccess(Closure $on_success) : self{
		return new self($on_success, static function(TebexException $exception) : void{
			self::getLogger()->logException($exception);
		});
	}

	/** @phpstan-var Closure(TTebexResponse) : void */
	public Closure $on_success;

	/** @phpstan-var Closure(TebexException) : void */
	public Closure $on_failure;

	/**
	 * @param Closure $on_success
	 * @param Closure $on_failure
	 *
	 * @phpstan-param Closure(TTebexResponse) : void $on_success
	 * @phpstan-param Closure(TebexException) : void $on_failure
	 */
	public function __construct(Closure $on_success, Closure $on_failure){
		$this->on_success = $on_success;
		$this->on_failure = $on_failure;
	}
}
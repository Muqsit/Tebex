<?php

declare(strict_types=1);

namespace muqsit\tebex\thread\response;

use muqsit\tebex\api\TebexResponse;
use muqsit\tebex\Loader;
use muqsit\tebex\thread\TebexException;
use Closure;
use pocketmine\Server;

final class TebexResponseHandler{

	public static function unhandled() : self{
		return self::onSuccess(static function(TebexResponse $response) : void{});
	}

	public static function debug() : self{
		return self::onSuccess(Closure::fromCallable("var_dump"));
	}

	public static function onSuccess(Closure $on_success) : self{
		static $logger = null;
		if($logger === null){
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("Tebex");
			if($plugin instanceof Loader){
				$logger = $plugin->getLogger();
			}
		}
		return new self($on_success, static function(TebexException $exception) use($logger) : void{
			$logger->critical($exception->getMessage());
		});
	}

	/**
	 * @var Closure
	 * @phpstan-var Closure(TebexResponse) : void
	 */
	public $on_success;

	/**
	 * @var Closure
	 * @phpstan-var Closure(TebexException) : void
	 */
	public $on_failure;

	public function __construct(Closure $on_success, Closure $on_failure){
		$this->on_success = $on_success;
		$this->on_failure = $on_failure;
	}
}
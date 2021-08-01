<?php

declare(strict_types=1);

namespace muqsit\tebex;

use InvalidStateException;
use muqsit\tebexapi\connection\response\TebexResponseHandler;
use muqsit\tebexapi\connection\SslConfiguration;
use muqsit\tebexapi\ConnectionBasedTebexApi;
use muqsit\tebexapi\endpoint\information\TebexInformation;
use muqsit\tebexapi\TebexApi;
use muqsit\tebexapi\TebexApiStatics;
use muqsit\tebexapi\utils\TebexException;
use muqsit\tebex\handler\command\RegisteredTebexCommandExecutor;
use muqsit\tebex\handler\command\TebexCommandSender;
use muqsit\tebex\handler\command\UnregisteredTebexCommandExecutor;
use muqsit\tebex\handler\PmmpTebexLogger;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\handler\ThreadedTebexConnection;
use muqsit\tebex\utils\TypedConfig;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use RuntimeException;

final class Loader extends PluginBase{

	private TebexInformation $information;
	private ?TebexHandler $handler = null;
	private ?ConnectionBasedTebexApi $api = null;
	private PluginCommand $command;
	private int $worker_limit;

	protected function onEnable() : void{
		if(!TebexCommandSender::hasInstance()){
			TebexCommandSender::setInstance(new TebexCommandSender($this, $this->getServer()->getLanguage()));
		}

		TebexApiStatics::setLogger(new PmmpTebexLogger($this->getLogger()));

		$command = new PluginCommand("tebex", $this, new UnregisteredTebexCommandExecutor($this));
		$command->setAliases(["tbx", "bc", "buycraft"]);
		$command->setPermission("tebex.admin");
		$this->getServer()->getCommandMap()->register($this->getName(), $command);
		$this->command = $command;

		$config = new TypedConfig($this->getConfig());

		$this->worker_limit = $config->getInt("worker-limit", 2);

		$secret = $config->getString("secret");

		try{
			$this->setSecret($secret);
		}catch(TebexException $e){
			$this->getLogger()->notice(($secret !== "" ? "{$e->getMessage()} " : "") . "Please configure your server's secret using: /{$this->command->getName()} secret <secret>");
			$this->command->setExecutor(new UnregisteredTebexCommandExecutor($this));
		}
	}

	protected function onDisable() : void{
		$this->handler?->shutdown();
		$this->api?->disconnect();
	}

	/**
	 * @param string $secret
	 * @return TebexInformation
	 * @throws TebexException
	 */
	public function setSecret(string $secret) : TebexInformation{
		/** @var TebexInformation|TebexException $result */
		$result = null;

		$_cacert_pem = $this->getResource("cacert.pem");
		if($_cacert_pem === null){
			throw new RuntimeException("Failed to locate SSL file cacert.pem");
		}

		$ssl_data = stream_get_contents($_cacert_pem);
		fclose($_cacert_pem);
		if($ssl_data === false){
			throw new RuntimeException("Failed to read contents of SSL file cacert.pem");
		}

		$api = new ConnectionBasedTebexApi(new ThreadedTebexConnection($this->getLogger(), $secret, SslConfiguration::fromData($ssl_data), $this->worker_limit));
		$api->getInformation(new TebexResponseHandler(
			static function(TebexInformation $information) use(&$result) : void{ $result = $information; },
			static function(TebexException $e) use(&$result) : void{ $result = $e; }
		));
		$api->wait();

		if($result instanceof TebexException){
			$api->disconnect();
			throw $result;
		}

		$this->init($api, $result);
		return $this->information;
	}

	private function init(ConnectionBasedTebexApi $api, TebexInformation $information) : void{
		$this->handler?->shutdown();
		$this->api?->disconnect();

		$this->api = $api;
		$this->information = $information;
		$this->handler = new TebexHandler($this);

		$executor = new RegisteredTebexCommandExecutor($this, $this->handler);
		foreach((new TypedConfig($this->getConfig()))->getStringList("disabled-sub-commands", []) as $disabled_sub_command){
			$executor->unregisterSubCommand($disabled_sub_command);
		}
		$this->command->setExecutor($executor);

		$account = $this->information->getAccount();
		$server = $this->information->getServer();
		$this->getLogger()->debug("Listening to events of \"{$server->getName()}\"[#{$server->getId()}] server as \"{$account->getName()}\"[#{$account->getId()}] (latency: " . round($this->getApi()->getLatency() * 1000) . "ms)");
	}

	public function getApi() : TebexApi{
		if($this->api === null){
			throw new InvalidStateException("API is not ready");
		}

		return $this->api;
	}

	public function getInformation() : TebexInformation{
		return $this->information;
	}
}
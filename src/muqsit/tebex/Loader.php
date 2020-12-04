<?php

declare(strict_types=1);

namespace muqsit\tebex;

use muqsit\tebex\api\information\TebexInformation;
use muqsit\tebex\handler\command\TebexCommandExecutor;
use muqsit\tebex\handler\command\TebexCommandSender;
use muqsit\tebex\handler\command\UnregisteredTebexCommandExecutor;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\thread\TebexException;
use muqsit\tebex\thread\response\TebexResponseHandler;
use muqsit\tebex\thread\ssl\SSLConfiguration;
use muqsit\tebex\utils\TypedConfig;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase{

	/** @var TebexInformation */
	private $information;

	/** @var TebexHandler */
	private $handler;

	/** @var TebexAPI */
	private $api;

	/** @var PluginCommand */
	private $command;

	/** @var int */
	private $worker_limit;

	protected function onEnable() : void{
		if(!TebexCommandSender::hasInstance()){
			TebexCommandSender::setInstance(new TebexCommandSender($this, $this->getServer()->getLanguage()));
		}

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
		if($this->handler !== null){
			$this->handler->shutdown();
		}

		if($this->api !== null){
			$this->api->shutdown();
		}
	}

	/**
	 * @param string $secret
	 * @return TebexInformation
	 * @throws TebexException
	 */
	public function setSecret(string $secret) : TebexInformation{
		/** @var TebexInformation|TebexException $result */
		$result = null;

		$api = new TebexAPI($this->getLogger(), $secret, SSLConfiguration::recommended(), $this->worker_limit);
		$api->getInformation(new TebexResponseHandler(
			static function(TebexInformation $information) use(&$result) : void{ $result = $information; },
			static function(TebexException $e) use(&$result) : void{ $result = $e; }
		));
		$api->waitAll();

		if($result instanceof TebexException){
			$api->shutdown();
			throw $result;
		}

		$this->init($api, $result);
		return $this->information;
	}

	private function init(TebexAPI $api, TebexInformation $information) : void{
		if($this->handler !== null){
			$this->handler->shutdown();
		}

		if($this->api !== null){
			$this->api->shutdown();
		}

		$this->api = $api;
		$this->information = $information;
		$this->handler = new TebexHandler($this);

		$executor = new TebexCommandExecutor($this, $this->handler);
		foreach((new TypedConfig($this->getConfig()))->getStringList("disabled-sub-commands", []) as $disabled_sub_command){
			$executor->unregisterSubCommand($disabled_sub_command);
		}
		$this->command->setExecutor($executor);

		$account = $this->information->getAccount();
		$server = $this->information->getServer();
		$this->getLogger()->debug("Listening to events of \"{$server->getName()}\"[#{$server->getId()}] server as \"{$account->getName()}\"[#{$account->getId()}] (latency: " . round($this->getApi()->getLatency() * 1000) . "ms)");
	}

	public function getApi() : TebexAPI{
		return $this->api;
	}

	public function getInformation() : TebexInformation{
		return $this->information;
	}
}
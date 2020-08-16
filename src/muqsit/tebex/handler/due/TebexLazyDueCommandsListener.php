<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

final class TebexLazyDueCommandsListener implements Listener{

	/** @var TebexDueCommandsHandler */
	private $handler;

	public function __construct(TebexDueCommandsHandler $handler){
		$this->handler = $handler;
	}

	/**
	 * @param PlayerLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerLoginEvent $event) : void{
		$this->handler->onPlayerJoin();
	}
}
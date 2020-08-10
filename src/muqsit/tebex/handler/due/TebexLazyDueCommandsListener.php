<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

final class TebexLazyDueCommandsListener implements Listener{

	/** @var TebexDueCommandsHandler */
	private $handler;

	public function __construct(TebexDueCommandsHandler $handler){
		$this->handler = $handler;
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$this->handler->onPlayerJoin();
	}
}
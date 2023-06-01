<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

final class TebexLazyDueCommandsListener implements Listener{

	public function __construct(
		readonly private TebexDueCommandsHandler $handler
	){}

	/**
	 * @param PlayerLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerLoginEvent $event) : void{
		$this->handler->onPlayerJoin();
	}
}
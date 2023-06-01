<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\due\playerlist;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class TebexDuePlayerListListener implements Listener{

	public function __construct(
		readonly private TebexDuePlayerList $list
	){}

	/**
	 * @param PlayerLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerLogin(PlayerLoginEvent $event) : void{
		$this->list->onPlayerJoin($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$this->list->onPlayerQuit($event->getPlayer());
	}
}
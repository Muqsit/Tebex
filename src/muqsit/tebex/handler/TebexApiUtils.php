<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use InvalidArgumentException;
use muqsit\tebexapi\endpoint\queue\TebexDuePlayer;
use muqsit\tebexapi\utils\TebexCommand;
use muqsit\tebexapi\utils\TebexGuiItem;
use muqsit\tebex\Loader;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;

final class TebexApiUtils{

	public static function convertGuiItemToItem(TebexGuiItem $gui_item) : Item{
		$item = StringToItemParser::getInstance()->parse($gui_item->getValue());
		if($item === null){
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("Tebex");
			if($plugin instanceof Loader){
				$plugin->getLogger()->warning("Failed to parse GUI item \"{$gui_item->getValue()}\", using PAPER as fallback");
			}
			return VanillaItems::PAPER();
		}
		return $item;
	}

	public static function onlineFormatCommand(TebexCommand $command, Player $player, TebexDuePlayer $due_player) : string{
		$gamertag = "\"{$player->getName()}\"";
		return strtr($command->asRawString(), [
			"{name}" => $gamertag,
			"{player}" => $gamertag,
			"{username}" => "\"{$due_player->getName()}\"",
			"{id}" => $player->getXuid()
		]);
	}

	public static function offlineFormatCommand(TebexCommand $command, TebexDuePlayer $due_player) : string{
		$gamertag = "\"{$due_player->getName()}\"";
		return strtr($command->asRawString(), [
			"{name}" => $gamertag,
			"{player}" => $gamertag,
			"{username}" => $gamertag,
			"{id}" => $due_player->getUuid() ?? ""
		]);
	}
}
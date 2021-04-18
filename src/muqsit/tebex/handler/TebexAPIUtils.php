<?php

declare(strict_types=1);

namespace muqsit\tebex\handler;

use InvalidArgumentException;
use muqsit\tebex\api\endpoint\queue\TebexDuePlayer;
use muqsit\tebex\api\utils\TebexCommand;
use muqsit\tebex\api\utils\TebexGUIItem;
use muqsit\tebex\Loader;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;

final class TebexAPIUtils{

	public static function convertGuiItemToItem(TebexGUIItem $gui_item) : Item{
		try{
			$item = VanillaItems::fromString($gui_item->getValue());
		}catch(InvalidArgumentException $e){
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("Tebex");
			if($plugin instanceof Loader){
				$plugin->getLogger()->warning("Failed to parse GUI item \"{$gui_item->getValue()}\", using PAPER as fallback");
			}else{
				throw $e;
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
<?php

declare(strict_types=1);

namespace muqsit\tebex\api\utils;

use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use muqsit\tebex\Loader;

final class TebexGUIItem{

	private string $value;

	public function __construct(string $value){
		$this->value = $value;
	}

	public function getValue() : string{
		return $this->value;
	}

	public function asItem() : ?Item{
		$item = null;
		try{
			$item = VanillaItems::fromString($this->value);
		}catch(InvalidArgumentException $e){
			$plugin = Server::getInstance()->getPluginManager()->getPlugin("Tebex");
			if($plugin instanceof Loader){
				$plugin->getLogger()->warning("Failed to parse GUI item \"{$this->value}\", using PAPER as fallback");
			}else{
				throw $e;
			}
			return VanillaItems::PAPER();
		}
		return $item;
	}
}
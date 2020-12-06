<?php

declare(strict_types=1);

namespace muqsit\tebex\handler\command;

use InvalidArgumentException;
use muqsit\tebex\api\coupons\create\TebexCouponBuilder;
use muqsit\tebex\api\coupons\create\TebexCouponCreateResponse;
use muqsit\tebex\api\coupons\TebexCouponsList;
use muqsit\tebex\api\utils\TebexDiscountInfo;
use muqsit\tebex\api\utils\time\DurationParser;
use muqsit\tebex\handler\command\utils\ClosureCommandExecutor;
use muqsit\tebex\handler\command\utils\TebexSubCommand;
use muqsit\tebex\handler\TebexHandler;
use muqsit\tebex\Loader;
use muqsit\tebex\thread\response\TebexResponseHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

final class TebexCommandExecutor extends UnregisteredTebexCommandExecutor{

	/** @var TebexHandler */
	private $handler;

	/** @var array<string, TebexSubCommand> */
	private $sub_commands = [];

	/** @var array<string, string> */
	private $aliases = [];

	public function __construct(Loader $plugin, TebexHandler $handler){
		parent::__construct($plugin);
		$this->handler = $handler;
		$this->registerDefaultSubCommands();
	}

	private function registerDefaultSubCommands() : void{
		$this->registerSubCommand(new TebexSubCommand("secret", "Set Tebex server secret", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				if(isset($args[1])){
					$this->onTypeSecret($sender, $command, $label, $args[1]);
				}else{
					$sender->sendMessage("Usage: /{$label} {$args[0]} <secret>");
				}
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("info", "Fetch Tebex account, server and API info", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				$info = $this->plugin->getInformation();
				$account = $info->getAccount();
				$server = $info->getServer();

				$sender->sendMessage(
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex Account" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "ID: " . TextFormat::GRAY . $account->getId() . TextFormat::EOL .
					TextFormat::WHITE . "Domain: " . TextFormat::GRAY . $account->getDomain() . TextFormat::EOL .
					TextFormat::WHITE . "Name: " . TextFormat::GRAY . $account->getName() . TextFormat::EOL .
					TextFormat::WHITE . "Currency: " . TextFormat::GRAY . "{$account->getCurrency()->getIso4217()} ({$account->getCurrency()->getSymbol()})" . TextFormat::EOL .
					TextFormat::WHITE . "Online Mode: " . TextFormat::GRAY . ($account->isOnlineModeEnabled() ? "Enabled" : "Disabled") . TextFormat::EOL .
					TextFormat::WHITE . "Game Type: " . TextFormat::GRAY . $account->getGameType() . TextFormat::EOL .
					TextFormat::WHITE . "Event Logging: " . TextFormat::GRAY . ($account->isLogEventsEnabled() ? "Enabled" : "Disabled") . TextFormat::EOL .
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex Server" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "ID: " . TextFormat::GRAY . $server->getId() . TextFormat::EOL .
					TextFormat::WHITE . "Name: " . TextFormat::GRAY . $server->getName() . TextFormat::EOL .
					"" . TextFormat::EOL .
					TextFormat::BOLD . TextFormat::WHITE . "Tebex API" . TextFormat::RESET . TextFormat::EOL .
					TextFormat::WHITE . "Latency: " . TextFormat::GRAY . round($this->plugin->getApi()->getLatency() * 1000) . "ms" . TextFormat::EOL .
					"" . TextFormat::EOL
				);
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("refresh", "Refresh offline and online command queues", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				static $command_senders_force_check = null;
				if($command_senders_force_check === null){
					/** @var CommandSender[] $command_senders_force_check */
					$command_senders_force_check = [];
					$this->handler->getDueCommandsHandler()->refresh(static function(int $offline_commands, int $online_players) use(&$command_senders_force_check) : void{
						if($command_senders_force_check !== null){
							foreach($command_senders_force_check as $sender){
								if(!($sender instanceof Player) || $sender->isOnline()){
									$sender->sendMessage(
										TextFormat::WHITE . "Refreshed command queue" . TextFormat::EOL .
										TextFormat::WHITE . "Offline commands fetched: " . TextFormat::GRAY . $offline_commands . TextFormat::EOL .
										TextFormat::WHITE . "Online players due: " . TextFormat::GRAY . $online_players
									);
								}
							}
							$command_senders_force_check = null;
						}
					});
				}

				$command_senders_force_check[spl_object_id($sender)] = $sender;
				$sender->sendMessage(TextFormat::GRAY . "Refreshing command queue...");
				return true;
			}
		), ["forcecheck"]));

		$this->registerSubCommand(new TebexSubCommand("dropall", "Drop all queued commands", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				static $command_senders_dropall = null;
				if($command_senders_dropall === null){
					/** @var CommandSender[] $command_senders_dropall */
					$command_senders_dropall = [];
					$this->handler->getDueCommandsHandler()->markAllAsExecuted(static function(int $marked) use(&$command_senders_dropall) : void{
						if($command_senders_dropall !== null){
							foreach($command_senders_dropall as $sender){
								if(!($sender instanceof Player) || $sender->isOnline()){
									$sender->sendMessage(TextFormat::WHITE . "Marked " . TextFormat::GRAY . $marked . TextFormat::WHITE . " command(s) as executed.");
								}
							}
							$command_senders_dropall = null;
						}
					});
				}

				$command_senders_dropall[spl_object_id($sender)] = $sender;
				$sender->sendMessage(TextFormat::GRAY . "Dropping all queued commands");
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("coupon_list", "List all coupons", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				$this->plugin->getApi()->getCoupons(TebexResponseHandler::onSuccess(function(TebexCouponsList $list) use ($sender){
					$coupons = $list->getCoupons();
					if(($count = count($coupons)) < 1){
						$sender->sendMessage(TextFormat::WHITE . "No active coupons available.");
						return;
					}
					$currency = $this->plugin->getInformation()->getAccount()->getCurrency()->getSymbol();
					$sender->sendMessage(TextFormat::WHITE . "Coupons ({$count}):");
					foreach($coupons as $coupon){
						$code = $coupon->isAvailable($sender) ? TextFormat::GREEN . $coupon->getCode() : TextFormat::RED . $coupon->getCode();
						$sender->sendMessage(TextFormat::WHITE . " - {$code}: {$coupon->getNote()}");
						$discount = $coupon->getDiscount();
						$sender->sendMessage(TextFormat::WHITE . "   - Discount: " . ($discount->getType() == TebexDiscountInfo::DISCOUNT_TYPE_PERCENTAGE ? $discount->getPercentage() . "%" : $currency . $discount->getValue()));
						if(($min = $coupon->getMinimum()) > 0) $sender->sendMessage(TextFormat::WHITE . "   - Minimum Spend: " . $currency . $min);
						if($coupon->getUsername() !== null) $sender->sendMessage(TextFormat::WHITE . "   - Username: " . $coupon->getUsername());
						// todo: other fields probably...
					}
				}));
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("coupon_add", "Add a new coupon", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				$currency = $this->plugin->getInformation()->getAccount()->getCurrency()->getSymbol();
				if(count($args) < 3){
					$sender->sendMessage("Usage: /tebex coupon_add <code|rand> <discount%|{$currency}discount> [expiryDuration (1M1d1h)] [redeemLimit=0] [minimumSpend=0] [username|none] [note]");
					return true;
				}

				$coupon = new TebexCouponBuilder();

				if($args[1] !== "rand"){
					if(preg_match("/^[a-zA-Z0-9-]+$/", $args[1]) !== 1) {
						$sender->sendMessage(TextFormat::WHITE . "Coupon code must only contain letters, numbers and dashes");
						return true;
					}
					$coupon->setCode($args[0]);
				}

				if($args[2][0] === $currency){
					$coupon->setDiscountAmount(round((float)substr($args[2], 1), 2));
					// todo: add limit
				} elseif ($args[2][strlen($args[2]) - 1] === "%"){
					$v = round((float)substr($args[2], 0, -1), 2);
					if($v > 100){
						$sender->sendMessage(TextFormat::WHITE . "Invalid discount: {$args[2]}, cannot go past 100%");
						return true;
					}
					$coupon->setDiscountPercentage($v);
				} else {
					$sender->sendMessage(TextFormat::WHITE . "Invalid discount: {$args[2]}, valid formats: 123.12%, {$currency}123.12");
					return true;
				}

				if(isset($args[3]) && $args[3] !== "never"){
					if(!DurationParser::isValid($args[3])){
						$sender->sendMessage(TextFormat::WHITE . "Invalid expiry date: {$args[3]}, valid format: 1w2d3h");
						return true;
					}
					$coupon->setExpiryDateFromTimestamp(DurationParser::parse($args[3]));
				}

				if(isset($args[4])){
					if(!is_numeric($args[4])){
						$sender->sendMessage(TextFormat::WHITE . "Invalid global redeem limit: {$args[4]}, valid format: 1, 5");
						return true;
					}
					$limit = (int)$args[4];
					if($limit < 0){
						$sender->sendMessage(TextFormat::WHITE . "Invalid global redeem limit: {$args[4]}, valid format: 0, 1+");
						return true;
					}
					if($limit > 0){
						$coupon->setRedeemLimit($limit);
					}
				}

				if(isset($args[5])){
					if(!is_numeric($args[5])){
						$sender->sendMessage(TextFormat::WHITE . "Invalid customer redeem limit: {$args[5]}, valid format: 1, 5");
						return true;
					}
					$limit = (int)$args[5];
					if($limit < 0){
						$sender->sendMessage(TextFormat::WHITE . "Invalid customer redeem limit: {$args[5]}, valid format: 0, 1+");
						return true;
					}
					if($limit > 0){
						$coupon->setUserLimit($limit);
					}
				}

				if(isset($args[6])){
					$args[6] = trim($args[6], $currency);
					if(!is_numeric($args[6])){
						$sender->sendMessage(TextFormat::WHITE . "Invalid minimum spend: {$args[6]}, valid format: 1.00, $1.00");
						return true;
					}
					$coupon->setMinimumBasketValue(round((float)$args[6], 2));
				}

				if(isset($args[7]) && $args[7] !== "none"){
					if(!Player::isValidUserName($args[7])){
						$sender->sendMessage(TextFormat::WHITE . "Invalid username: {$args[7]}. valid format: Steve, Herobrine");
						return true;
					}
					$coupon->setUsername($args[7]);
				}

				if(count($args) > 8){
					$coupon->setNote(implode(" ", array_slice($args, 7)));
				}

				$this->plugin->getApi()->createCoupon($coupon->build(), TebexResponseHandler::onSuccess(function(TebexCouponCreateResponse $response) use ($sender){
					$sender->sendMessage(TextFormat::WHITE . "Created coupon code: {$response->code}");
				}));
				return true;
			}
		)));

		$this->registerSubCommand(new TebexSubCommand("coupon_remove", "Delete a coupon", new ClosureCommandExecutor(
			function(CommandSender $sender, Command $command, string $label, array $args) : bool{
				if(count($args) < 2){
					$sender->sendMessage("Usage: /tebex coupon_remove <code>");
					return true;
				}
				$this->plugin->getApi()->getCoupons(TebexResponseHandler::onSuccess(function(TebexCouponsList $list) use ($sender, $args) {
					foreach($list->getCoupons() as $coupon){
						if($coupon->getCode() === $args[1]){
							$this->plugin->getApi()->deleteCoupon($coupon->getId());
							$sender->sendMessage("Deleted coupon with code {$coupon->getCode()}");
							return true;
						}
					}
					$sender->sendMessage("Unable to find coupon with code {$args[1]}");
					return true;
				}));
				return true;
			}
		)));
	}

	public function registerSubCommand(TebexSubCommand $sub_command) : void{
		$this->sub_commands[$sub_command->name] = $sub_command;
		foreach($sub_command->aliases as $alias){
			$this->aliases[$alias] = $sub_command->name;
		}
	}

	public function unregisterSubCommand(string $name) : void{
		if(!isset($this->sub_commands[$name])){
			throw new InvalidArgumentException("Tried unregistering an unregistered sub-command: {$name}");
		}
		foreach($this->sub_commands[$name]->aliases as $alias){
			unset($this->aliases[$alias]);
		}
		unset($this->sub_commands[$name]);
	}

	public function getSubCommand(string $name) : ?TebexSubCommand{
		return $this->sub_commands[$name] ?? (isset($this->aliases[$name]) ? $this->sub_commands[$this->aliases[$name]] : null);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(isset($args[0])){
			$sub_command = $this->getSubCommand($args[0]);
			if($sub_command !== null){
				return $sub_command->executor->onCommand($sender, $command, $label, $args);
			}
		}

		$help = TextFormat::BOLD . TextFormat::WHITE . "Tebex Commands" . TextFormat::RESET . TextFormat::EOL;
		/** @var TebexSubCommand $sub_command */
		foreach($this->sub_commands as $sub_command){
			$help .= TextFormat::WHITE . "/{$label} {$sub_command->name}" . TextFormat::GRAY . " - {$sub_command->description}" . TextFormat::EOL;
		}
		$sender->sendMessage(rtrim($help, TextFormat::EOL));
		return false;
	}
}
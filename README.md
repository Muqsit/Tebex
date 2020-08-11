# Tebex
Tebex webstore integration for PocketMine-MP.

## Features
- `disable-sub-commands` configuration so you can disable `/tebex secret` and other risky sub-commands when running in production.
- Running tebex API calls over a dedicated child thread so the main thread doesn't lag when checking for pending commands, running `/tebex refresh` etc.

## Developer Docs
At the moment, there isn't a method to interact with this plugin. The plugin however has the Tebex API and handling split — `tebex/handler/*` makes use of TebexAPI.
You can create a new TebexAPI instance and supply any valid secret to it and call tebex endpoints. All method calls in TebexAPI are run on child thread(s) so you'll need to supply
a `TebexResponseHandler $callback` to retrieve responses.
```php
TebexAPI::getInformation(TebexResponseHandler $callback) : void
```
You may construct a `new TebexResponseHandler(Closure<TebexResponse, void> $on_success, Closure<TebexException, void> $on_failure)`, or use the helper methods:
- `TebexResponseHandler::debug(string $expected_response_class = TebexResponse::class)` — `var_dump`s the response.
- `TebexResponseHandler::onSuccess(Closure<TebexResponse, void> $on_success)` — Calls `$on_success` on success and logs (level: critical) error message on failure.
```php
$secret = "";
$worker_limit = 1;
$api = new TebexAPI(MainLogger::getLogger(), $secret, SSLConfiguration::recommended(), $worker_limit);

$api->getInformation(TebexResponseHandler::onSuccess(function(TebexInformation $information) : void{
	$account_info = $this->information->getAccount();
	$server_info = $this->information->getServer();
}));

$api->lookup("Steve", TebexResponseHandler::onSuccess(function(TebexUser $user) : void{
	var_dump($user->getChargebackRate());
}));

$api->lookup("Alex", TebexResponseHandler::debug(TebexUser::class)); // var_dump()s the TebexUser on success

$api->waitAll(); // wait until all queued requests have received responses
$api->shutdown(); // shutdown connection (stops all threads and unlinks temp SSL files)
```
Not all Tebex endpoints have been implemented in TebexAPI. You may create an issue or a PR adding the missing ones or create one yourself (take a look at classes in `tebex\api`, use `TebexAPI::request(TebexRequest, TebexResponseHandler)` to dispatch custom ones).

## Notes
The [official Tebex plugin for PocketMine](https://github.com/tebexio/BuycraftPM) runs refresh tasks on main thread, freezing the server based on Tebex <-> Your Minecraft Server
latency. It executes Tebex API calls while having `CURLOPT_SSL_VERIFYPEER` disabled, thereby allowing man-in-the-middle (MITM) attacks and has some unnecessary commands that
do not suit Bedrock Edition (the `/buy` GUI command) which is why I decided to write a tebex integration plugin from scratch.

<?php

declare(strict_types=1);

namespace muqsit\tebex\thread;

use muqsit\tebex\api\TebexResponse;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use Throwable;
use function is_string;
use Logger;
use muqsit\tebex\api\TebexRequest;
use muqsit\tebex\TebexAPI;
use muqsit\tebex\thread\request\TebexRequestHolder;
use muqsit\tebex\thread\response\TebexResponseFailureHolder;
use muqsit\tebex\thread\response\TebexResponseHandler;
use muqsit\tebex\thread\response\TebexResponseHolder;
use muqsit\tebex\thread\response\TebexResponseSuccessHolder;
use muqsit\tebex\thread\ssl\SSLConfiguration;
use Generator;
use JsonException;
use Threaded;

final class TebexThread extends Thread{

	/**
	 * @var TebexResponseHandler[]
	 *
	 * @phpstan-var TebexResponseHandler<TebexResponse>[]
	 */
	private static array $handlers = [];

	private static int $handler_ids = 0;

	/** @var SleeperNotifier<mixed> */
	private SleeperNotifier $notifier;

	/** @var Threaded<string> */
	private Threaded $incoming;

	/** @var Threaded<string> */
	private Threaded $outgoing;

	private Logger $logger;
	public int $busy_score = 0;
	private bool $running = false;
	private string $secret;
	private string $ca_path;

	/**
	 * @param Logger $logger
	 * @param SleeperNotifier<mixed> $notifier
	 * @param string $secret
	 * @param SSLConfiguration $ssl_config
	 */
	public function __construct(Logger $logger, SleeperNotifier $notifier, string $secret, SSLConfiguration $ssl_config){
		$this->notifier = $notifier;
		$this->ca_path = $ssl_config->getCAInfoPath();
		$this->incoming = new Threaded();
		$this->outgoing = new Threaded();
		$this->logger = $logger;
		$this->secret = $secret;
	}

	/**
	 * @param TebexRequest $request
	 * @param TebexResponseHandler $handler
	 *
	 * @phpstan-template TTebexResponse of \muqsit\tebex\api\TebexResponse
	 * @phpstan-param TebexRequest<TTebexResponse> $request
	 * @phpstan-param TebexResponseHandler<TTebexResponse> $handler
	 */
	public function push(TebexRequest $request, TebexResponseHandler $handler) : void{
		$handler_id = ++self::$handler_ids;
		$this->incoming[] = igbinary_serialize(new TebexRequestHolder($request, $handler_id));
		self::$handlers[$handler_id] = $handler;
		++$this->busy_score;
		$this->synchronized(function() : void{
			$this->notifyOne();
		});
	}

	/**
	 * @return array<int, mixed>
	 */
	private function getDefaultCurlOptions() : array{
		$curl_opts = [
			CURLOPT_HTTPHEADER => [
				"X-Tebex-Secret: {$this->secret}",
				"User-Agent: Tebex"
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 5,
		];
		if($this->ca_path !== ""){
			$curl_opts[CURLOPT_CAINFO] = $this->ca_path;
		}
		return $curl_opts;
	}

	protected function onRun() : void{
		$this->running = true;
		$default_curl_opts = $this->getDefaultCurlOptions();
		while($this->running){
			while(($request_serialized = $this->incoming->shift()) !== null){
				assert(is_string($request_serialized));
				/** @var TebexRequestHolder $request_holder */
				$request_holder = igbinary_unserialize($request_serialized);

				$request = $request_holder->request;

				$url = TebexAPI::BASE_ENDPOINT . $request->getEndpoint();
				$this->logger->debug("[cURL] Executing request: {$url}");

				$latency = 5000;
				$ch = curl_init($url);
				if($ch === false){
					$response_holder = new TebexResponseFailureHolder($request_holder->handler_id, $latency, new TebexException("cURL request failed during initialization"));
				}else{
					$body = false;
					try{
						$curl_opts = $default_curl_opts;
						$request->addAdditionalCurlOpts($curl_opts);
						curl_setopt_array($ch, $curl_opts);

						$body = curl_exec($ch);

						/** @var float $latency */
						$latency = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

						if(!is_string($body)){
							throw new TebexException("cURL request failed {" . curl_errno($ch) . "): " . curl_error($ch));
						}

						/** @var int $response_code */
						$response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

						if($response_code !== $request->getExpectedResponseCode()){
							try{
								/** @var array{error_message: string} $message_body */
								$message_body = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
							}catch(JsonException $e){
								$message_body = [];
							}
							throw new TebexException($message_body["error_message"] ?? "Expected response code {$request->getExpectedResponseCode()}, got {$response_code}");
						}

						if($body === ""){
							$result = [];
						}else{
							$result = null;
							try{
								/** @phpstan-var array<string, mixed>|null $result */
								$result = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
							}catch(JsonException $e){
								$result = null;
								throw new TebexException("{$e->getMessage()} during parsing JSON body: " . base64_encode($body));
							}

							if($result === null){
								throw new TebexException("Error during parsing JSON body: " . base64_encode($body));
							}
						}

						if(isset($result["error_code"], $result["error_message"])){
							assert(is_string($result["error_message"]));
							throw new TebexException($result["error_message"]);
						}

						$response_holder = new TebexResponseSuccessHolder($request_holder->handler_id, $latency, $request->createResponse($result));
					}catch(TebexException $e){
						$response_holder = new TebexResponseFailureHolder($request_holder->handler_id, $latency, $e);
					}catch(Throwable $e){
						if(is_string($body)){
							$this->logger->info("An error occurred while parsing request: " . base64_encode($body));
						}
						$this->logger->logException($e);
						throw $e;
					}finally{
						curl_close($ch);
					}
				}

				$this->outgoing[] = igbinary_serialize($response_holder);
				$this->notifier->wakeupSleeper();
			}

			$this->sleep();
		}
	}

	public function sleep() : void{
		$this->synchronized(function() : void{
			if($this->running){
				$this->wait();
			}
		});
	}

	public function stop() : void{
		$this->running = false;
		$this->synchronized(function() : void{
			$this->notify();
		});
	}

	/**
	 * Collects all responses and returns the total latency
	 * (in seconds) in sending request and getting response.
	 *
	 * @return Generator<float>
	 */
	public function collectPending() : Generator{
		while(($holder_serialized = $this->outgoing->shift()) !== null){
			assert(is_string($holder_serialized));

			/** @var TebexResponseHolder<TebexResponse> $holder */
			$holder = igbinary_unserialize($holder_serialized);

			$holder->trigger(self::$handlers[$holder->handler_id]);
			unset(self::$handlers[$holder->handler_id]);
			--$this->busy_score;

			yield $holder->latency;
		}
	}
}
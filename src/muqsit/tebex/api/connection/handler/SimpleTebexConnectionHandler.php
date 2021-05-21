<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection\handler;

use JsonException;
use muqsit\tebex\api\connection\request\TebexRequestHolder;
use muqsit\tebex\api\connection\response\TebexResponseFailureHolder;
use muqsit\tebex\api\connection\response\TebexResponseHolder;
use muqsit\tebex\api\connection\response\TebexResponseSuccessHolder;
use muqsit\tebex\api\TebexApiStatics;
use muqsit\tebex\api\utils\TebexException;
use RuntimeException;
use Throwable;

/**
 * A simple implementation of a cURL handler.
 */
final class SimpleTebexConnectionHandler implements TebexConnectionHandler{

	public function handle(TebexRequestHolder $request_holder, array $default_curl_options) : TebexResponseHolder{
		$request = $request_holder->request;

		$url = TebexApiStatics::ENDPOINT . $request->getEndpoint();

		$latency = 5000;
		$ch = curl_init($url);
		if($ch === false){
			$response_holder = new TebexResponseFailureHolder($request_holder->handler_id, $latency, new TebexException("cURL request failed during initialization"));
		}else{
			$body = false;
			try{
				$curl_opts = $default_curl_options;
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
				throw new RuntimeException("An error occurred while parsing request: " . (is_string($body) ? base64_encode($body) : json_encode($body, JSON_THROW_ON_ERROR)), is_int($e->getCode()) ? $e->getCode() : 0, $e);
			}finally{
				curl_close($ch);
			}
		}

		return $response_holder;
	}
}
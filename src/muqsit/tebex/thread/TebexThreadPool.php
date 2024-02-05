<?php

declare(strict_types=1);

namespace muqsit\tebex\thread;

use muqsit\tebexapi\connection\handler\TebexConnectionHandler;
use pocketmine\Server;
use pocketmine\snooze\SleeperHandlerEntry;
use UnderflowException;
use function count;

final class TebexThreadPool{

	readonly public SleeperHandlerEntry $sleeper_handler_entry;

	/** @var array<int, TebexThread> */
	private array $workers = [];

	private float $latency = 0.0;

	public function __construct(
		readonly public TebexConnectionHandler $connection_handler
	){
		$this->sleeper_handler_entry = Server::getInstance()->getTickSleeper()->addNotifier(function() : void{
			foreach($this->workers as $thread){
				$this->collectThread($thread);
			}
		});
	}

	/**
	 * @param TebexThread<mixed> $thread
	 */
	public function addWorker(TebexThread $thread) : void{
		$this->workers[spl_object_id($thread)] = $thread;
	}

	public function start() : void{
		count($this->workers) > 0 || throw new UnderflowException("Cannot start an empty pool of workers");
		foreach($this->workers as $thread){
			$thread->start();
		}
	}

	/**
	 * @return TebexThread<mixed>
	 */
	public function getLeastBusyWorker() : TebexThread{
		$best = null;
		$best_score = INF;
		foreach($this->workers as $thread){
			$score = $thread->busy_score;
			if($score < $best_score){
				$best_score = $score;
				$best = $thread;
				if($score === 0){
					break;
				}
			}
		}
		assert($best !== null);
		return $best;
	}

	public function getLatency() : float{
		return $this->latency;
	}

	public function waitAll(int $sleep_duration_ms) : void{
		foreach($this->workers as $thread){
			while($thread->busy_score > 0){
				usleep($sleep_duration_ms);
				$this->collectThread($thread);
			}
		}
	}

	/**
	 * @param TebexThread<mixed> $thread
	 */
	private function collectThread(TebexThread $thread) : void{
		foreach($thread->collectPending() as $latency){
			$this->latency = $latency;
		}
	}

	public function shutdown() : void{
		foreach($this->workers as $thread){
			$thread->stop();
			$thread->join();
		}
		$this->workers = [];
	}
}
<?php

declare(strict_types=1);

namespace app\workers;

use app\common\Log;
use app\database\PDOHelper;

/**
 * Class Parallel
 * @package app\workers
 */
class Parallel {
    /** @var WorkerInterface */
    protected $workers = [];

    public function __construct(array $workers) {
        foreach ($workers as $worker) {
            if (!($worker instanceof WorkerInterface)) {
                throw new \Exception('worker instance must be provided');
            }
        }
        $this->workers = $workers;
    }

    public function run() {
        $processIds = [];
        $pid = null;
        $activeWorker = null;
        Log::setMessagePrefix(\sprintf('parent[%u]', \getmypid()));
        foreach ($this->workers as $worker) {
            $pid = \pcntl_fork();
            if ($pid == -1) {
                die('failed to fork');
            } else if ($pid) {
                // This is parent process.
                $processIds[] = $pid;
                Log::info(\sprintf('started child process %u', $pid));
            } else {
                // Child process.
                $activeWorker = $worker;
                break;
            }
        }
        if ($activeWorker) {
            Log::empty();
            Log::setDelimiterAfterLog("\n");
            Log::setMessagePrefix(\sprintf('child[%u]', \getmypid()));
            PDOHelper::reconnect();
            $result = $activeWorker->run();
            return $result;
        } else {
            // Wait until all child processes finish
            foreach ($processIds as $pid) {
                $pid = \pcntl_wait($status);
                Log::info(\sprintf('child process %u finished', $pid));
            }
        }
        return true;
    }
}

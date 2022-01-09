<?php

declare(strict_types=1);

namespace app\workers;

use app\models\TaskModel;

/**
 * Class AbstractWorker
 * @package app\workers
 */
abstract class AbstractWorker implements WorkerInterface {
    /** @var TaskModel|null */
    protected $task = null;

    public function __construct(TaskModel $task)
    {
        $this->task = $task;
    }

    /**
     * @return array
     * @throws \Exception
     */
    final public function run(): array {
        $this->task->started = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $this->task->update();
        $result = $this->processTask();
        $this->task->finished = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $this->task->result = $result;
        $this->task->update();
        return $result;
    }

    abstract public function processTask(): array;
}

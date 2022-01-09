<?php

declare(strict_types=1);

namespace app\workers;

use app\models\TaskModel;

/**
 * Interface WorkerInterface
 * @package app\workers
 */
interface WorkerInterface {
    public function __construct(TaskModel $task);
    public function run(): array;
}

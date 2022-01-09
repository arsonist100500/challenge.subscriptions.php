<?php

declare(strict_types=1);

namespace app\cli\command;

use app\common\Log;
use app\database\PDOHelper;
use app\models\TaskModel;
use app\models\UserModel;
use app\workers\ExpirationEmailWorker;
use app\workers\Parallel;

/**
 * Class SendNotificationsCommand
 * @package app\cli\command
 */
class SendNotificationsCommand implements CommandInterface {
    /** @var int */
    protected $processesAmount = 0;
    /** @var UserModel[] */
    protected $users = [];
    /** @var TaskModel[] */
    protected $tasks = [];

    public function run(): int {
        global $argv;
        $options = \array_slice($argv, 2);
        $this->processesAmount = (int)($options[0] ?? 10);
        $this->getUsers($this->processesAmount);
        Log::info(sprintf('found %u users', \count($this->users)));
        if (!empty($this->users)) {
            $this->createTasks();
            $this->runWorkers();
        }
        return 0;
    }

    protected function getUsers(int $limit): void {
        [$tsMin, $tsMax] = [time() + 2*86400, time() + 3*86400];
        $pdo = PDOHelper::connect();
        $sql = $this->getUsersQuery($tsMin, $tsMax, $limit);
        $statement = $pdo->prepare($sql);
        if ($statement) {
            if ($statement->execute()) {
                do {
                    $row = $statement->fetch(\PDO::FETCH_ASSOC);
                    if ($row) {
                        $this->users[] = new UserModel($row);
                    }
                } while ($row);
            }
        }
    }

    protected function getUsersQuery(int $tsMin, int $tsMax, int $limit): string {
        return \trim("
            SELECT u.id, u.username, u.email, u.validts
            FROM users u
            LEFT JOIN emails e ON e.email = u.email
            WHERE u.validts BETWEEN $tsMin AND $tsMax
            AND u.notified IS NULL
            AND (u.confirmed = 1 OR e.valid = 1)
            AND NOT EXISTS (SELECT id FROM tasks t WHERE t.email = u.email)
            LIMIT $limit
        ");
    }

    protected function createTasks(): void {
        foreach ($this->users as $user) {
            try {
                $task = new TaskModel([
                    'email' => $user->email,
                    'input' => [
                        'username' => $user->username,
                        'expiration' => (new \DateTime('@' . $user->validts))->format('Y-m-d H:i:s'),
                    ],
                ]);
                $task->insert();
                $this->tasks[] = $task;
            } catch (\Exception $e) {
                \printf('Failed to create task for user %s: %s\n', $user->username, $e->getMessage());
            }
        }
    }

    protected function runWorkers(): void {
        $workers = [];
        foreach ($this->tasks as $task) {
            $workers[] = new ExpirationEmailWorker($task);
        }
        $parallel = new Parallel($workers);
        $parallel->run();
    }
}

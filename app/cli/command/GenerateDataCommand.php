<?php

declare(strict_types=1);

namespace app\cli\command;

use app\common\Config;
use app\common\Log;
use app\common\TimeHelper;
use app\database\PDOHelper;

/**
 * Class GenerateDataCommand
 * @package app\cli\command
 */
class GenerateDataCommand implements CommandInterface {
    protected const BATCH_SIZE = 10000;

    protected $data = [
        'names' => [],
        'surnames' => [],
        'domains' => [],
    ];
    protected $users = [];
    protected $emails = [];

    public function run(): int {
        global $argv;
        $options = \array_slice($argv, 2);
        $amount = $options[0] ?? 100;
        TimeHelper::measureFunction(function () use ($amount) {
            $this->loadData();
            for ($i = 0; $i < $amount; ++$i) {
                $this->generateUser();
            }
        }, $seconds);
        Log::info(\sprintf('generated %u users, %u emails in %.3f ms', \count($this->users), \count($this->emails), $seconds*1000));
        $this->batchInsertUsers();
        $this->batchInsertEmails();
        return 0;
    }

    protected function loadData(): void {
        $this->data['names'] = Config::get('userData.names');
        $this->data['surnames'] = Config::get('userData.surnames');
        $this->data['domains'] = Config::get('userData.domains');
    }

    protected function generateUser(): void {
        $name = $this->chooseRandom($this->data['names']);
        $surname = $this->chooseRandom($this->data['surnames']);
        $domain = $this->chooseRandom($this->data['domains']);
        $email = \strtolower(\sprintf('%s.%s@%s', $name, $surname, $domain));
        $checked = (int)(\rand(0, 999) < 500);
        $valid = (int)($checked ? \rand(0, 999) < 800 : 0);
        $this->users[] = [
            'username' => $name . ' ' . $surname,
            'email' => $email,
            'validts' => \time() + rand(1000, 86400*10),
            'confirmed' => (int)(\rand(0, 999) < 500),
        ];
        $this->emails[] = [
            'email' => $email,
            'checked' => $checked,
            'valid' => $valid,
        ];
    }

    protected function chooseRandom(array $a) {
        if (!empty($a)) {
            $index = \rand(0, count($a) - 1);
            return $a[$index];
        }
        return null;
    }

    protected function batchInsert(string $table, array $rows): void {
        $pdo = PDOHelper::connect();
        $inserted = 0;
        $insertedTotal = 0;
        $batches = \array_chunk($rows, self::BATCH_SIZE);
        foreach ($batches as $batch) {
            TimeHelper::measureFunction(function () use ($pdo, $table, $batch, & $inserted, & $insertedTotal) {
                try {
                    $inserted = 0;
                    $pdo->beginTransaction();
                    foreach ($batch as $row) {
                        $id = PDOHelper::insert($table, $row, $error);
                        if ($id) {
                            $inserted += 1;
                        }
                    }
                    $pdo->commit();
                    $insertedTotal += $inserted;
                } catch (\Throwable $e) {
                    $pdo->rollBack();
                }
            }, $seconds);
            Log::info(\sprintf(
                'inserted %u rows into "%s", progress %u/%u (%.3f ms)',
                $inserted,
                $table,
                $insertedTotal,
                \count($this->users),
                $seconds*1000
            ));
        }
        return;
    }

    protected function batchInsertUsers(): void {
        $this->batchInsert('users', $this->users);
    }

    protected function batchInsertEmails(): void {
        $this->batchInsert('emails', $this->emails);
    }
}

<?php

declare(strict_types=1);

namespace app\cli\command;

use app\common\AppPath;
use app\database\Migration;

/**
 * Class MigrateCommand
 * @package app\cli\command
 */
class MigrateCommand implements CommandInterface {

    public function run(): int {
        $migrations = [];
        $iterator = new \DirectoryIterator(AppPath::MIGRATIONS);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isFile() && $fileInfo->isReadable()) {
                $migrations[$fileInfo->getFilename()] = new Migration($fileInfo->getRealPath());
            }
        }
        \ksort($migrations);
        foreach ($migrations as $migration) {
            $migration->run();
        }
        return 0;
    }
}

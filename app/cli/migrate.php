<?php

declare(strict_types=1);

namespace app\cli;

use app\common\AppPath;
use app\database\Migration;

require_once(__DIR__ . '/../common/Autoload.php');

function main() {
    $app = new CliApp(function () {
        $migrations = [];
        $iterator = new \DirectoryIterator(AppPath::MIGRATIONS);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isFile() && $fileInfo->isReadable()) {
                $migrations[$fileInfo->getFilename()] = new Migration($fileInfo->getRealPath());
            }
        }
        ksort($migrations);
        foreach ($migrations as $migration) {
            $migration->run();
        }
    });
    $app->run();
}

main();

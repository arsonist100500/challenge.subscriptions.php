<?php

declare(strict_types=1);

namespace app\cli;

use app\common\AppPath;
use app\database\Migration;

require_once(__DIR__ . '/../common/Autoload.php');

function main() {
    $app = new CliApp(function () {
        $iterator = new \DirectoryIterator(AppPath::MIGRATIONS);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isFile() && $fileInfo->isReadable()) {
                $migration = new Migration($fileInfo->getRealPath());
                $migration->run();
            }
        }
    });
    $app->run();
}

main();

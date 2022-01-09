<?php

declare(strict_types=1);

namespace app\database;

use app\common\Log;
use PDO;

class Migration {
    /** @var string|null */
    protected $filename = null;

    function __construct(string $filename) {
        $fileInfo = new \SplFileInfo($filename);
        if ($fileInfo->isFile() && $fileInfo->isReadable() && $fileInfo->getExtension() === 'sql') {
            $this->filename = $fileInfo->getRealPath();
        }
    }

    public function run(): bool {
        if ($this->filename) {
            $query = file_get_contents($this->filename);
            $pdo = PDOHelper::connect();
            $result = $pdo->exec($query);
            $result = $result !== false;
            if ($result) {
                Log::info('applied migration: ' . \basename($this->filename));
            }
            return (bool)$result;
        }
        return false;
    }
}

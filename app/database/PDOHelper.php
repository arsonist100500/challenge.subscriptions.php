<?php

declare(strict_types=1);

namespace app\database;

use PDO;
use app\common\Log;

class PDOHelper {
    /** @var PDO|null  */
    protected static $connection = null;

    public static function connect(): ?PDO {
        if (self::$connection) {
            return self::$connection;
        }
        $host = DatabaseConfig::get('host') ?? 'localhost';
        $port = (int)(DatabaseConfig::get('port') ?? 3306);
        $user = DatabaseConfig::get('user') ?? null;
        $password = DatabaseConfig::get('password') ?? null;
        $dbname = DatabaseConfig::get('dbname') ?? null;
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=UTF8";
        try {
            $pdo = new PDO($dsn, $user, $password);
            if ($pdo) {
                self::$connection = $pdo;
                Log::debug("connected to the $dbname database");
            }
            return $pdo;
        } catch (\PDOException $e) {
            Log::error('got exception: ' . $e->getMessage());
        }
        return null;
    }
}

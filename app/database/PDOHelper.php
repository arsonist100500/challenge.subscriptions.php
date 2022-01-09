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

    /**
     * @param string $table
     * @param array $values
     * @return bool|string
     * @throws \Exception
     */
    public static function insert(string $table, array $values) {
        if (empty($values)) {
            return false;
        }
        if (!self::connect()) {
            throw new \Exception('unable to connect');
        }
        $columns = [];
        $bind = [];
        foreach ($values as $column => $value) {
            $column = \preg_replace('/[^a-zA-Z0-9_-]/', '', $column);
            $columns[] = $column;
            $bind[':' . $column] = $value;
        }
        $sql = \sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            \implode(', ', $columns),
            \implode(', ', \array_keys($bind))
        );
        $pdo = self::$connection;
        $statement = $pdo->prepare($sql);
        if ($statement->execute($bind)) {
            return $pdo->lastInsertId();
        }
        return false;
    }

    /**
     * @param string $table
     * @param array $set
     * @param array $where
     * @return bool
     * @throws \Exception
     */
    public static function update(string $table, array $set, array $where): bool {
        if (empty($set)) {
            return false;
        }
        if (!self::connect()) {
            throw new \Exception('unable to connect');
        }
        $expressionsSet = [];
        $expressionsWhere = [];
        $bind = [];
        foreach ($set as $column => $value) {
            $column = \preg_replace('/[^a-zA-Z0-9_-]/', '', $column);
            $expressionsSet[] = $column . '=' . ':' . $column;
            $bind[':' . $column] = $value;
        }
        foreach ($where as $column => $value) {
            $column = \preg_replace('/[^a-zA-Z0-9_-]/', '', $column);
            $expressionsWhere[] = $column . '=' . ':' . $column;
            $bind[':' . $column] = $value;
        }
        $sql = \sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            \implode(', ', $expressionsSet),
            \implode(' AND ', $expressionsWhere)
        );
        $pdo = self::$connection;
        $statement = $pdo->prepare($sql);
        return $statement->execute($bind);
    }
}

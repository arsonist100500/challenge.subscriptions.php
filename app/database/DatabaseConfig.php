<?php

declare(strict_types=1);

namespace app\database;

use app\common\Config;

class DatabaseConfig extends Config {
    public static function get(string $name) {
        $db = parent::get('env.db');
        $name = sprintf('database.%s.%s', $db, $name);
        return parent::get($name);
    }
}

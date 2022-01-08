<?php

declare(strict_types=1);

namespace app\common;

class AppPath {
    public const ROOT = __DIR__ . '/../../';
    public const CONFIG = self::ROOT . '/config/';
    public const LOGS = self::ROOT . '/logs/';
}

<?php

declare(strict_types=1);

namespace app\common;

class TimeHelper {
    public static function measureFunction(callable $fCallback, & $dTime) {
        $dStart = \microtime(true);
        $result = \call_user_func($fCallback);
        $dTime = \round(\microtime(true) - $dStart, 6);
        return $result;
    }
}

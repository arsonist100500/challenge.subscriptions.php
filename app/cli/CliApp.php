<?php

declare(strict_types=1);

namespace app\cli;

use app\common\AppPath;
use app\common\Log;

class CliApp {
    protected $callback = null;

    function __construct(callable $fn) {
        $this->callback = $fn;
    }

    /**
     * @throws \Exception
     */
    public function run() {
        try {
            Log::setVerbosity(Log::DEBUG);
            Log::setFlagPrintVerbosity(true);
            Log::setLogFileName(AppPath::LOGS . '/cli.log');
            call_user_func($this->callback);
        } catch (\Exception $e) {
            printf('Exception: %s\n', $e->getMessage());
            printf('Trace:\n%s\n', $e->getTraceAsString());
        }
    }
}

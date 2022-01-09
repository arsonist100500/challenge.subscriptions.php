<?php

declare(strict_types=1);

namespace app\cli;

use app\cli\command\CommandInterface;
use app\common\AppPath;
use app\common\Log;

class CliApp {
    /**
     * CliApp constructor.
     * @throws \Exception
     */
    function __construct() {
        if (\php_sapi_name() !== 'cli') {
            throw new \Exception('must be invoked in command line interface');
        }
    }

    public function run() {
        global $argv;
        try {
            Log::setVerbosity(Log::DEBUG);
            Log::setFlagPrintVerbosity(true);
            Log::setLogFileName(AppPath::LOGS . '/cli.log');

            if (count($argv) < 2) {
                $this->usage();
                exit(1);
            }
            $commandName = $argv[1];
            $commandName = str_replace('-', '', ucwords($commandName, '-'));
            $className = __NAMESPACE__ . '\\command\\' . $commandName . 'Command';
            if (\class_exists($className)) {
                /** @var CommandInterface $command */
                $command = new $className();
                $exitCode = $command->run();
                exit($exitCode);
            }
        } catch (\Exception $e) {
            printf('Exception: %s\n', $e->getMessage());
            printf('Trace:\n%s\n', $e->getTraceAsString());
        }
    }

    protected function usage(): void {
        global $argv;
        \printf("Usage: php %s COMMAND\n", $argv[0]);
    }
}

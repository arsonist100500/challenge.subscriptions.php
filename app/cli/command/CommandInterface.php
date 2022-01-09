<?php

declare(strict_types=1);

namespace app\cli\command;

/**
 * Interface CommandInterface
 * @package app\cli
 */
interface CommandInterface {
    /**
     * @return int Exit code
     */
    public function run(): int;
}

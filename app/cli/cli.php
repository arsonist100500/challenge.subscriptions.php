<?php

declare(strict_types=1);

namespace app\cli;

require_once(__DIR__ . '/../common/Autoload.php');

$app = new CliApp();
$app->run();

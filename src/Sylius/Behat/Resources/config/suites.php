<?php

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/api.php',
        'suites/cli.php',
        'suites/domain.php',
        'suites/hybrid.php',
        'suites/ui.php',
    ]);

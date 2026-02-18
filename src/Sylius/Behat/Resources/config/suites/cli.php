<?php

use Behat\Config\Config;

return (new Config())
    ->import([
        'cli/canceling_unpaid_orders.php',
        'cli/change_admin_password.php',
        'cli/installer.php',
    ]);

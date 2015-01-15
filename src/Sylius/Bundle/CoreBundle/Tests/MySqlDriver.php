<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests;

use Doctrine\DBAL\Driver\PDOMySql\Driver;

class MySqlDriver extends Driver
{
    private static $connection;

    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        if (null === self::$connection) {
            self::$connection = parent::connect($params, $username, $password, $driverOptions);
        }

        return self::$connection;
    }
}

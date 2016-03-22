<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test;

use Doctrine\DBAL\Driver\PDOPgSql\Driver;

final class PgSqlDriver extends Driver
{
    /**
     * @var Driver
     */
    private static $connection;

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (null === self::$connection) {
            self::$connection = parent::connect($params, $username, $password, $driverOptions);
        }

        return self::$connection;
    }
}

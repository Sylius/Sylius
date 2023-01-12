<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional\Doctrine\Mock;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

class ConnectionMock extends Connection
{
    /** @var DatabasePlatformMock */
    private $_platformMock;

    public function __construct(array $params = [], ?Driver $driver = null, ?Configuration $config = null, ?EventManager $eventManager = null)
    {
        $this->_platformMock = new DatabasePlatformMock();

        parent::__construct($params, $driver ?? new DriverMock(), $config, $eventManager);
    }

    public function getDatabasePlatform()
    {
        return $this->_platformMock;
    }
}

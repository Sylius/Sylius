<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional\Doctrine\Mock;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class DriverMock implements Driver
{
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        return new DriverConnectionMock();
    }

    public function getDatabasePlatform()
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function getSchemaManager(Connection $conn, ?AbstractPlatform $platform = null): AbstractSchemaManager
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function getName()
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function getDatabase(Connection $conn)
    {
        throw new \BadMethodCallException('Not implemented');
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\DatabaseData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\DatabasePlatformDataProvider;

final class DatabasePlatformDataProviderTest extends TestCase
{
    private Connection $connection;
    private ManagerRegistry $managerRegistry;
    private DatabasePlatformDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->provider = new DatabasePlatformDataProvider($this->managerRegistry);
    }

    public function test_it_provides_database_information(): void
    {
        $this->connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $this->managerRegistry->method('getConnection')->willReturn($this->connection);

        $data = $this->provider->provide();

        self::assertInstanceOf(DatabaseData::class, $data);
        self::assertNotNull($data->type);
    }

    public function test_it_returns_mysql_type_for_mysql_database(): void
    {
        $this->connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $this->managerRegistry->method('getConnection')->willReturn($this->connection);

        $data = $this->provider->provide();

        self::assertInstanceOf(DatabaseData::class, $data);
        self::assertSame('mysql', $data->type);
    }

    public function test_it_handles_connection_errors_gracefully(): void
    {
        $this->managerRegistry->method('getConnection')->willThrowException(new \RuntimeException('Connection failed'));

        $data = $this->provider->provide();

        self::assertInstanceOf(DatabaseData::class, $data);
        self::assertNull($data->type);
        self::assertNull($data->version);
    }

    public function test_it_handles_platform_errors_gracefully(): void
    {
        $this->connection->method('getDatabasePlatform')->willThrowException(new \RuntimeException('Platform error'));
        $this->connection->method('getParams')->willReturn([]);
        $this->managerRegistry->method('getConnection')->willReturn($this->connection);

        $data = $this->provider->provide();

        self::assertInstanceOf(DatabaseData::class, $data);
    }
}

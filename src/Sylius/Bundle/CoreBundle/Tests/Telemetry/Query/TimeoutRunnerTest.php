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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class TimeoutRunnerTest extends TestCase
{
    public function test_mysql_applies_optimizer_hint(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::matchesRegularExpression('/SELECT\s+\/\*\+\s+MAX_EXECUTION_TIME\(5000\)\s+\*\//'),
                [],
            )
            ->willReturn([['id' => 1]]);

        $runner = new TimeoutRunner(5000);
        $result = $runner->fetchAllAssociative($connection, 'SELECT id FROM sylius_order');

        self::assertSame([['id' => 1]], $result);
    }

    public function test_mariadb_applies_set_statement(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(MariaDBPlatform::class));
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                'SET STATEMENT max_statement_time=5 FOR SELECT id FROM sylius_order',
                [],
            )
            ->willReturn([['id' => 1]]);

        $runner = new TimeoutRunner(5000);
        $result = $runner->fetchAllAssociative($connection, 'SELECT id FROM sylius_order');

        self::assertSame([['id' => 1]], $result);
    }

    public function test_postgresql_uses_transaction_with_set_local(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(PostgreSQLPlatform::class));
        $connection->expects(self::once())->method('beginTransaction');
        $connection->expects(self::once())
            ->method('executeStatement')
            ->with("SET LOCAL statement_timeout = '5000'");
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with('SELECT id FROM sylius_order', [])
            ->willReturn([['id' => 1]]);
        $connection->expects(self::once())->method('commit');
        $connection->expects(self::never())->method('rollBack');

        $runner = new TimeoutRunner(5000);
        $result = $runner->fetchAllAssociative($connection, 'SELECT id FROM sylius_order');

        self::assertSame([['id' => 1]], $result);
    }

    public function test_postgresql_rolls_back_on_exception(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(PostgreSQLPlatform::class));
        $connection->expects(self::once())->method('beginTransaction');
        $connection->expects(self::once())->method('executeStatement');
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->willThrowException(new \RuntimeException('query timeout'));
        $connection->expects(self::never())->method('commit');
        $connection->expects(self::once())->method('rollBack');

        $runner = new TimeoutRunner(5000);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('query timeout');

        $runner->fetchAllAssociative($connection, 'SELECT id FROM sylius_order');
    }

    public function test_default_timeout_is_60_seconds(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::matchesRegularExpression('/MAX_EXECUTION_TIME\(60000\)/'),
                [],
            )
            ->willReturn([]);

        $runner = new TimeoutRunner();
        $runner->fetchAllAssociative($connection, 'SELECT id FROM sylius_order');
    }

    public function test_fetch_associative_applies_timeout(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::matchesRegularExpression('/MAX_EXECUTION_TIME\(3000\)/'),
                [],
            )
            ->willReturn(['count' => 42]);

        $runner = new TimeoutRunner(3000);
        $result = $runner->fetchAssociative($connection, 'SELECT COUNT(*) as count FROM sylius_order');

        self::assertSame(['count' => 42], $result);
    }

    public function test_fetch_first_column_applies_timeout(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $connection->expects(self::once())
            ->method('fetchFirstColumn')
            ->with(
                self::matchesRegularExpression('/MAX_EXECUTION_TIME\(3000\)/'),
                [],
            )
            ->willReturn(['USD', 'EUR']);

        $runner = new TimeoutRunner(3000);
        $result = $runner->fetchFirstColumn($connection, 'SELECT code FROM sylius_currency');

        self::assertSame(['USD', 'EUR'], $result);
    }

    public function test_mariadb_timeout_converts_to_seconds(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($this->createMock(MariaDBPlatform::class));
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                'SET STATEMENT max_statement_time=2.5 FOR SELECT 1',
                [],
            )
            ->willReturn([]);

        $runner = new TimeoutRunner(2500);
        $runner->fetchAllAssociative($connection, 'SELECT 1');
    }
}

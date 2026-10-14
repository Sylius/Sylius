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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\SkipMigration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class AbstractMigrationTest extends TestCase
{
    /** @return iterable<string, array{AbstractPlatform}> */
    public static function mysqlPlatforms(): iterable
    {
        yield 'mysql' => [new MySQLPlatform()];
        yield 'mariadb' => [new MariaDBPlatform()];
    }

    #[DataProvider('mysqlPlatforms')]
    public function test_it_does_not_skip_migration_on_mysql_and_mariadb_platforms(AbstractPlatform $platform): void
    {
        $migration = $this->createMigration($platform);

        $migration->preUp(new Schema());
        $migration->preDown(new Schema());

        $this->expectNotToPerformAssertions();
    }

    public function test_it_skips_migration_on_other_platforms(): void
    {
        $this->expectException(SkipMigration::class);

        $this->createMigration(new PostgreSQLPlatform())->preUp(new Schema());
    }

    private function createMigration(AbstractPlatform $platform): AbstractMigration
    {
        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($platform);

        return new class($connection, new NullLogger()) extends AbstractMigration {
            public function up(Schema $schema): void
            {
            }
        };
    }
}

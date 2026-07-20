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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\Platform;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\Platform\PlatformHelper;

final class PlatformHelperTest extends TestCase
{
    public function test_it_recognizes_mysql_platform(): void
    {
        self::assertTrue(PlatformHelper::isMysql(new MySQLPlatform()));
    }

    public function test_it_recognizes_mariadb_platform(): void
    {
        self::assertTrue(PlatformHelper::isMysql(new MariaDBPlatform()));
    }

    /** Versioned MariaDB platform classes were renamed between dbal 3.x and 4.x, so we test the ancestor match instead of a specific class name. */
    public function test_it_recognizes_any_mysql_or_mariadb_platform_subclass(): void
    {
        $versionedPlatform = new class extends AbstractMySQLPlatform {
        };

        self::assertTrue(PlatformHelper::isMysql($versionedPlatform));
    }

    public function test_it_does_not_recognize_other_platforms_as_mysql(): void
    {
        self::assertFalse(PlatformHelper::isMysql(new PostgreSQLPlatform()));
        self::assertFalse(PlatformHelper::isMysql(new SQLitePlatform()));
    }

    public function test_it_recognizes_postgresql_platform(): void
    {
        self::assertTrue(PlatformHelper::isPostgreSQL(new PostgreSQLPlatform()));
        self::assertFalse(PlatformHelper::isPostgreSQL(new MySQLPlatform()));
    }

    public function test_it_recognizes_sqlite_platform(): void
    {
        self::assertTrue(PlatformHelper::isSqlite(new SQLitePlatform()));
        self::assertFalse(PlatformHelper::isSqlite(new MySQLPlatform()));
    }
}

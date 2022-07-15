<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

abstract class AbstractMigration extends BaseAbstractMigration
{
    public function preUp(Schema $schema): void
    {
        $this->abortIf(!$this->isMySql(), 'Migration can only be executed safely on \'mysql\'.');
    }

    public function preDown(Schema $schema): void
    {
        $this->abortIf(!$this->isMySql(), 'Migration can only be executed safely on \'mysql\'.');
    }

    protected function isMariaDb(): bool
    {
        $platform = $this->connection->getDatabasePlatform();

        /** @psalm-suppress DeprecatedClass */
        if (class_exists(\Doctrine\DBAL\Platforms\MariaDb1027Platform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MariaDb1027Platform::class)) {
            return true;
        }

        /** @psalm-suppress DeprecatedClass */
        if (class_exists(\Doctrine\DBAL\Platforms\MariaDBPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MariaDBPlatform::class)) {
            return true;
        }

        return false;
    }

    protected function isMySql(): bool
    {
        $platform = $this->connection->getDatabasePlatform();

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress InvalidClass
         */
        if (class_exists(\Doctrine\DBAL\Platforms\MySQLPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MySQLPlatform::class, true)) {
            return true;
        }

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress InvalidClass
         */
        if (class_exists(\Doctrine\DBAL\Platforms\MySqlPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MySqlPlatform::class, true)) {
            return true;
        }

        return false;
    }
}

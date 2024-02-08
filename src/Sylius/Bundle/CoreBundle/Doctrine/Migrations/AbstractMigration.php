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

        if (class_exists(\Doctrine\DBAL\Platforms\MariaDb1027Platform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MariaDb1027Platform::class)) {
            return true;
        }

        if (class_exists(\Doctrine\DBAL\Platforms\MariaDBPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MariaDBPlatform::class)) {
            return true;
        }

        return false;
    }

    protected function isMySql(): bool
    {
        $platform = $this->connection->getDatabasePlatform();

        /** @phpstan-ignore-next-line */
        if ($this->classExistsCaseSensitive(\Doctrine\DBAL\Platforms\MySQLPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MySQLPlatform::class, true)) {
            return true;
        }

        /** @phpstan-ignore-next-line */
        if ($this->classExistsCaseSensitive(\Doctrine\DBAL\Platforms\MySqlPlatform::class) && is_a($platform, \Doctrine\DBAL\Platforms\MySqlPlatform::class, true)) {
            return true;
        }

        return false;
    }

    private function classExistsCaseSensitive(string $className): bool
    {
        return class_exists(strtolower($className)) && (new \ReflectionClass($className))->getName() === $className;
    }
}

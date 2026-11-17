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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

abstract class AbstractMigration extends BaseAbstractMigration implements MigrationSkipInterface
{
    public function preUp(Schema $schema): void
    {
        $this->skipIf(!$this->isMySql(), 'This migration can only be executed on \'MySQL\'.');
    }

    public function preDown(Schema $schema): void
    {
        $this->skipIf(!$this->isMySql(), 'This migration can only be executed on \'MySQL\'.');
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
        return $this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform;
    }

    /** @deprecated */
    protected function markAsExecuted(string $version): void
    {
        $this->connection->executeQuery(
            sprintf('INSERT INTO sylius_migrations (version, executed_at) VALUES (\'%s\', NOW())', $version),
        );
        $this->connection->commit();
    }

    protected function classExistsCaseSensitive(string $className): bool
    {
        return class_exists(strtolower($className)) && (new \ReflectionClass($className))->getName() === $className;
    }
}

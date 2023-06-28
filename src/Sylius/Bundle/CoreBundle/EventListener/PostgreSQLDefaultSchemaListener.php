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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

final class PostgreSQLDefaultSchemaListener
{
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $connection = $args->getEntityManager()->getConnection();

        $schemaManager = $this->createSchemaManager($connection);

        if (!is_a($schemaManager, PostgreSQLSchemaManager::class)) {
            return;
        }

        $schema = $args->getSchema();

        foreach ($schemaManager->listSchemaNames() as $namespace) {
            if (!$schema->hasNamespace($namespace)) {
                $schema->createNamespace($namespace);
            }
        }
    }

    private function createSchemaManager(Connection $connection): AbstractSchemaManager
    {
        if (method_exists($connection, 'createSchemaManager')) {
            return $connection->createSchemaManager();
        }

        if (method_exists($connection, 'getSchemaManager')) {
            /** @psalm-suppress DeprecatedMethod */
            return $connection->getSchemaManager();
        }

        throw new \RuntimeException('Cannot create schema manager');
    }
}

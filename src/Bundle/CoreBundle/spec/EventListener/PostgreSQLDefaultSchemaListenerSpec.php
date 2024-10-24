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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\MySQLSchemaManager;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PhpSpec\ObjectBehavior;

final class PostgreSQLDefaultSchemaListenerSpec extends ObjectBehavior
{
    function it_does_nothing_if_schema_manager_is_not_postgresql(
        Connection $connection,
        EntityManagerInterface $entityManager,
        GenerateSchemaEventArgs $args,
        MySQLSchemaManager $schemaManager,
    ): void {
        $connection->createSchemaManager()->willReturn($schemaManager);
        $entityManager->getConnection()->willReturn($connection);
        $args->getEntityManager()->willReturn($entityManager);

        $args->getSchema()->shouldNotBeCalled();
        $schemaManager->listSchemaNames()->shouldNotBeCalled();

        $this->postGenerateSchema($args);
    }

    function it_creates_namespaces_for_all_schemas_in_the_current_database_if_schema_manager_is_postgresql(
        Connection $connection,
        EntityManagerInterface $entityManager,
        GenerateSchemaEventArgs $args,
        PostgreSQLSchemaManager $schemaManager,
        Schema $schema,
    ): void {
        $connection->createSchemaManager()->willReturn($schemaManager);
        $entityManager->getConnection()->willReturn($connection);
        $args->getEntityManager()->willReturn($entityManager);

        $schemaManager->listSchemaNames()->willReturn(['public', 'information_schema']);

        $args->getSchema()->willReturn($schema);

        $schema->hasNamespace('public')->willReturn(false);
        $schema->hasNamespace('information_schema')->willReturn(false);
        $schema->createNamespace('public')->shouldBeCalled();
        $schema->createNamespace('information_schema')->shouldBeCalled();

        $this->postGenerateSchema($args);
    }
}

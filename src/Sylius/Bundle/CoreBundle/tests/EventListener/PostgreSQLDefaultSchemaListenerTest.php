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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\MySQLSchemaManager;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\PostgreSQLDefaultSchemaListener;

final class PostgreSQLDefaultSchemaListenerTest extends TestCase
{
    private Connection&MockObject $connection;

    private EntityManagerInterface&MockObject $entityManager;

    private GenerateSchemaEventArgs&MockObject $args;

    private MockObject&PostgreSQLSchemaManager $pgSchemaManager;

    private MockObject&MySQLSchemaManager $mySqlSchemaManager;

    private MockObject&Schema $schema;

    private PostgreSQLDefaultSchemaListener $listener;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->args = $this->createMock(GenerateSchemaEventArgs::class);
        $this->pgSchemaManager = $this->createMock(PostgreSQLSchemaManager::class);
        $this->mySqlSchemaManager = $this->createMock(MySQLSchemaManager::class);
        $this->schema = $this->createMock(Schema::class);
        $this->listener = new PostgreSQLDefaultSchemaListener();
    }

    public function testDoesNothingIfSchemaManagerIsNotPostgresql(): void
    {
        $this->connection->method('createSchemaManager')->willReturn($this->mySqlSchemaManager);
        $this->entityManager->method('getConnection')->willReturn($this->connection);
        $this->args->method('getEntityManager')->willReturn($this->entityManager);

        $this->args->expects($this->never())->method('getSchema');
        $this->mySqlSchemaManager->expects($this->never())->method('listSchemaNames');

        $this->listener->postGenerateSchema($this->args);
    }

    public function testCreatesNamespacesForAllSchemasInCurrentDatabaseIfSchemaManagerIsPostgresql(): void
    {
        $this->connection->method('createSchemaManager')->willReturn($this->pgSchemaManager);
        $this->entityManager->method('getConnection')->willReturn($this->connection);
        $this->args->method('getEntityManager')->willReturn($this->entityManager);
        $this->pgSchemaManager->method('listSchemaNames')->willReturn(['public', 'information_schema']);
        $this->args->method('getSchema')->willReturn($this->schema);
        $this->schema->method('hasNamespace')->willReturnMap([
            ['public', false],
            ['information_schema', false],
        ]);

        $calledNamespaces = [];
        $schema = $this->schema;
        $this->schema
            ->expects($this->exactly(2))
            ->method('createNamespace')
            ->with($this->isType('string'))
            ->willReturnCallback(function (string $namespace) use (&$calledNamespaces, $schema) {
                $calledNamespaces[] = $namespace;

                return $schema;
            })
        ;

        $this->listener->postGenerateSchema($this->args);

        $this->assertEqualsCanonicalizing(['public', 'information_schema'], $calledNamespaces);
    }
}

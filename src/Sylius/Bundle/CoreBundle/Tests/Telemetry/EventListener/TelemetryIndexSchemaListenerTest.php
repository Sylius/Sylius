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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\EventListener\TelemetryIndexSchemaListener;

final class TelemetryIndexSchemaListenerTest extends TestCase
{
    private const TABLE_NAME = 'sylius_order';

    private const INDEX_NAME = 'IDX_TELEMETRY_ORDER_STATS';

    private Connection $connection;

    private AbstractSchemaManager $schemaManager;

    private TelemetryIndexSchemaListener $listener;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->schemaManager = $this->createMock(AbstractSchemaManager::class);
        $this->connection->method('createSchemaManager')->willReturn($this->schemaManager);

        $this->listener = new TelemetryIndexSchemaListener($this->connection);
    }

    public function test_it_does_nothing_when_table_does_not_exist_in_schema(): void
    {
        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(false);

        $this->schemaManager->expects($this->never())->method('listTableIndexes');

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);
    }

    public function test_it_does_nothing_when_index_does_not_exist_in_database(): void
    {
        $table = $this->createMock(Table::class);
        $table->expects($this->never())->method('addIndex');

        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(true);
        $schema->method('getTable')->with(self::TABLE_NAME)->willReturn($table);

        $this->schemaManager->method('listTableIndexes')->with(self::TABLE_NAME)->willReturn([]);

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);
    }

    public function test_it_adds_index_to_schema_when_index_exists_in_database_but_not_in_schema(): void
    {
        $dbIndex = new Index(
            self::INDEX_NAME,
            ['checkout_completed_at', 'checkout_state', 'payment_state'],
        );

        $table = $this->createMock(Table::class);
        $table->method('hasIndex')->with(self::INDEX_NAME)->willReturn(false);
        $table->expects($this->once())->method('addIndex')->with(
            ['checkout_completed_at', 'checkout_state', 'payment_state'],
            self::INDEX_NAME,
            [],
            [],
        );

        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(true);
        $schema->method('getTable')->with(self::TABLE_NAME)->willReturn($table);

        $this->schemaManager->method('listTableIndexes')->with(self::TABLE_NAME)->willReturn([
            strtolower(self::INDEX_NAME) => $dbIndex,
        ]);

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);
    }

    public function test_it_does_nothing_when_index_already_exists_in_schema(): void
    {
        $dbIndex = new Index(
            self::INDEX_NAME,
            ['checkout_completed_at', 'checkout_state', 'payment_state'],
        );

        $table = $this->createMock(Table::class);
        $table->method('hasIndex')->with(self::INDEX_NAME)->willReturn(true);
        $table->expects($this->never())->method('addIndex');

        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(true);
        $schema->method('getTable')->with(self::TABLE_NAME)->willReturn($table);

        $this->schemaManager->method('listTableIndexes')->with(self::TABLE_NAME)->willReturn([
            strtolower(self::INDEX_NAME) => $dbIndex,
        ]);

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);
    }

    public function test_it_handles_database_exceptions_silently(): void
    {
        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(true);

        $this->schemaManager->method('listTableIndexes')->willThrowException(new \RuntimeException('DB error'));

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);

        $this->assertTrue(true);
    }

    public function test_it_handles_lowercase_index_name_from_postgresql(): void
    {
        $dbIndex = new Index(
            strtolower(self::INDEX_NAME),
            ['checkout_completed_at', 'checkout_state', 'payment_state'],
        );

        $table = $this->createMock(Table::class);
        $table->method('hasIndex')->with(self::INDEX_NAME)->willReturn(false);
        $table->expects($this->once())->method('addIndex');

        $schema = $this->createMock(Schema::class);
        $schema->method('hasTable')->with(self::TABLE_NAME)->willReturn(true);
        $schema->method('getTable')->with(self::TABLE_NAME)->willReturn($table);

        $this->schemaManager->method('listTableIndexes')->with(self::TABLE_NAME)->willReturn([
            strtolower(self::INDEX_NAME) => $dbIndex,
        ]);

        $event = $this->createEvent($schema);

        $this->listener->postGenerateSchema($event);
    }

    private function createEvent(Schema $schema): GenerateSchemaEventArgs
    {
        return new GenerateSchemaEventArgs(
            $this->createMock(EntityManagerInterface::class),
            $schema,
        );
    }
}

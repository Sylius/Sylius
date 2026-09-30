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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\Schema\LegacyTypeCommentsSchemaListener;
use Sylius\Bundle\PaymentBundle\Doctrine\DBAL\Type\ObjectType;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[CoversClass(LegacyTypeCommentsSchemaListener::class)]
final class LegacyTypeCommentsSchemaListenerTest extends TestCase
{
    private AbstractSchemaManager $schemaManager;

    private EntityManagerInterface $entityManager;

    private LegacyTypeCommentsSchemaListener $listener;

    protected function setUp(): void
    {
        if (method_exists(Type::class, 'requiresSQLCommentHint')) {
            $this->markTestSkipped('DBAL 3.x manages the legacy type comments on its own.');
        }

        $this->registerType('object', ObjectType::class);
        $this->registerType('uuid', UuidType::class);

        $this->schemaManager = $this->createMock(AbstractSchemaManager::class);

        $connection = $this->createMock(Connection::class);
        $connection->method('createSchemaManager')->willReturn($this->schemaManager);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($connection);

        $this->listener = new LegacyTypeCommentsSchemaListener();
    }

    public function test_it_restores_the_legacy_comment_of_a_payment_security_token_details(): void
    {
        $schema = $this->createSchema('sylius_payment_security_token', ['details' => 'object']);

        $this->schemaManager
            ->method('listTableColumns')
            ->with('sylius_payment_security_token')
            ->willReturn([$this->createDatabaseColumn('details', 'object', '(DC2Type:object)')])
        ;

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame(
            '(DC2Type:object)',
            $schema->getTable('sylius_payment_security_token')->getColumn('details')->getComment(),
        );
    }

    public function test_it_restores_the_legacy_comments_of_all_payment_request_columns_with_a_single_introspection(): void
    {
        $schema = $this->createSchema('sylius_payment_request', ['hash' => 'uuid', 'payload' => 'object']);

        $this->schemaManager
            ->expects($this->once())
            ->method('listTableColumns')
            ->with('sylius_payment_request')
            ->willReturn([
                $this->createDatabaseColumn('hash', 'uuid', '(DC2Type:uuid)'),
                $this->createDatabaseColumn('payload', 'object', '(DC2Type:object)'),
            ])
        ;

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $table = $schema->getTable('sylius_payment_request');

        $this->assertSame('(DC2Type:uuid)', $table->getColumn('hash')->getComment());
        $this->assertSame('(DC2Type:object)', $table->getColumn('payload')->getComment());
    }

    public function test_it_does_not_restore_the_legacy_comment_when_the_database_does_not_have_it(): void
    {
        $schema = $this->createSchema('sylius_payment_request', ['payload' => 'object']);

        $this->schemaManager
            ->method('listTableColumns')
            ->willReturn([$this->createDatabaseColumn('payload', 'object', '')])
        ;

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame('', $schema->getTable('sylius_payment_request')->getColumn('payload')->getComment());
    }

    public function test_it_does_not_restore_the_legacy_comment_when_the_column_has_been_remapped_to_another_type(): void
    {
        $schema = $this->createSchema('sylius_payment_request', ['payload' => 'json']);

        $this->schemaManager->expects($this->never())->method('listTableColumns');

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame('', $schema->getTable('sylius_payment_request')->getColumn('payload')->getComment());
    }

    public function test_it_does_not_touch_a_column_with_a_comment_of_its_own(): void
    {
        $schema = $this->createSchema('sylius_payment_request', ['payload' => 'object']);
        $schema->getTable('sylius_payment_request')->getColumn('payload')->setComment('The payload');

        $this->schemaManager->expects($this->never())->method('listTableColumns');

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame('The payload', $schema->getTable('sylius_payment_request')->getColumn('payload')->getComment());
    }

    public function test_it_does_not_touch_tables_outside_of_the_legacy_list(): void
    {
        $schema = $this->createSchema('sylius_promotion_coupon', ['track_usage_since' => 'datetime_immutable']);
        $messengerTable = $schema->createTable('messenger_messages');
        $messengerTable->addColumn('created_at', 'datetime_immutable');

        $this->schemaManager->expects($this->never())->method('listTableColumns');

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame('', $schema->getTable('sylius_promotion_coupon')->getColumn('track_usage_since')->getComment());
        $this->assertSame('', $messengerTable->getColumn('created_at')->getComment());
    }

    public function test_it_does_not_introspect_the_database_when_the_table_is_absent_from_the_schema(): void
    {
        $this->schemaManager->expects($this->never())->method('listTableColumns');

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, new Schema()));
    }

    public function test_it_ignores_the_database_being_unreachable(): void
    {
        $schema = $this->createSchema('sylius_payment_request', ['payload' => 'object']);

        $this->schemaManager
            ->method('listTableColumns')
            ->willThrowException(new \RuntimeException('The database does not exist.'))
        ;

        $this->listener->postGenerateSchema(new GenerateSchemaEventArgs($this->entityManager, $schema));

        $this->assertSame('', $schema->getTable('sylius_payment_request')->getColumn('payload')->getComment());
    }

    /** @param array<string, string> $columns */
    private function createSchema(string $tableName, array $columns): Schema
    {
        $schema = new Schema();
        $table = $schema->createTable($tableName);

        foreach ($columns as $columnName => $typeName) {
            $table->addColumn($columnName, $typeName);
        }

        return $schema;
    }

    private function createDatabaseColumn(string $name, string $typeName, string $comment): Column
    {
        $column = new Column($name, Type::getType($typeName));
        $column->setComment($comment);

        return $column;
    }

    /** @param class-string<Type> $className */
    private function registerType(string $name, string $className): void
    {
        if (!Type::hasType($name)) {
            Type::addType($name, $className);
        }
    }
}

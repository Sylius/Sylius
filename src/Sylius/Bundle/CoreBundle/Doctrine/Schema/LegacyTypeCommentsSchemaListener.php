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

namespace Sylius\Bundle\CoreBundle\Doctrine\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

/** @internal */
final class LegacyTypeCommentsSchemaListener
{
    /** @var array<string, array<string, string>> table name => column name => DBAL type name */
    private const LEGACY_COMMENTED_COLUMNS = [
        'sylius_payment_request' => [
            'hash' => 'uuid',
            'payload' => 'object',
        ],
        'sylius_payment_security_token' => [
            'details' => 'object',
        ],
    ];

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        if (!self::areLegacyTypeCommentsRemoved()) {
            return;
        }

        $schema = $args->getSchema();
        $schemaManager = $args->getEntityManager()->getConnection()->createSchemaManager();

        foreach (self::LEGACY_COMMENTED_COLUMNS as $tableName => $columnTypes) {
            if (!$schema->hasTable($tableName)) {
                continue;
            }

            $table = $schema->getTable($tableName);
            $databaseColumns = null;

            foreach ($columnTypes as $columnName => $typeName) {
                if (!$table->hasColumn($columnName)) {
                    continue;
                }

                $column = $table->getColumn($columnName);

                if ($column->getComment() !== '' || !$this->isOfType($column, $typeName)) {
                    continue;
                }

                $databaseColumns ??= $this->getDatabaseColumns($schemaManager, $tableName);
                $legacyComment = sprintf('(DC2Type:%s)', $typeName);

                if (($databaseColumns[strtolower($columnName)] ?? null)?->getComment() !== $legacyComment) {
                    continue;
                }

                $column->setComment($legacyComment);
            }
        }
    }

    private function isOfType(Column $column, string $typeName): bool
    {
        $typeRegistry = Type::getTypeRegistry();

        return $typeRegistry->has($typeName) && $typeRegistry->get($typeName) === $column->getType();
    }

    /**
     * @param AbstractSchemaManager<AbstractPlatform> $schemaManager
     *
     * @return array<string, Column> keyed by lower-cased column name
     */
    private function getDatabaseColumns(AbstractSchemaManager $schemaManager, string $tableName): array
    {
        try {
            $databaseColumns = $schemaManager->listTableColumns($tableName);
        } catch (\Throwable) {
            return [];
        }

        $columns = [];
        foreach ($databaseColumns as $databaseColumn) {
            $columns[strtolower($databaseColumn->getName())] = $databaseColumn;
        }

        return $columns;
    }

    private static function areLegacyTypeCommentsRemoved(): bool
    {
        return !method_exists(Type::class, 'requiresSQLCommentHint');
    }
}

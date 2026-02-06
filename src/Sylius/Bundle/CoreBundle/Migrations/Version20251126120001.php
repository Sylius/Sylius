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

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

final class Version20251126120001 extends AbstractPostgreSQLMigration
{
    private const INDEX_NAME = 'IDX_TELEMETRY_ORDER_STATS';

    private const TABLE_NAME = 'sylius_order';

    public function getDescription(): string
    {
        return 'Add index on sylius_order for telemetry queries optimization (PostgreSQL)';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable(self::TABLE_NAME)) {
            return;
        }

        $existingIndexes = $this->getExistingIndexesNames(self::TABLE_NAME);

        if (in_array(self::INDEX_NAME, $existingIndexes, true)) {
            return;
        }

        $this->addSql('CREATE INDEX ' . self::INDEX_NAME . ' ON ' . self::TABLE_NAME . ' (checkout_completed_at, checkout_state, payment_state)');
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable(self::TABLE_NAME)) {
            return;
        }

        $existingIndexes = $this->getExistingIndexesNames(self::TABLE_NAME);

        if (!in_array(self::INDEX_NAME, $existingIndexes, true)) {
            return;
        }

        $this->addSql('DROP INDEX ' . self::INDEX_NAME);
    }

    /** @return list<string> */
    private function getExistingIndexesNames(string $tableName): array
    {
        if (method_exists($this->connection, 'createSchemaManager')) {
            $indexes = $this->connection->createSchemaManager()->listTableIndexes($tableName);
        } else {
            /** @phpstan-ignore method.notFound (DBAL 3.x compatibility) */
            $indexes = $this->connection->getSchemaManager()->listTableIndexes($tableName);
        }

        $indexesNames = [];
        foreach ($indexes as $index) {
            $indexesNames[] = $index->getName();
        }

        return $indexesNames;
    }
}

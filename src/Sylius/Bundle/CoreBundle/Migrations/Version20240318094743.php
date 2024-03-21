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

final class Version20240318094743 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Replace DC2TYPE:array with JSONB';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->changeTypesFromLongtextToJsonAndEncodeSerializedData($table, $column);
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->changeTypesFromJsonToText($table, $column);
        }
    }

    public function postDown(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->serializeEncodedData($table, $column);
        }
    }

    private function changeTypesFromLongtextToJsonAndEncodeSerializedData(string $table, string $dataColumn): void
    {
        if ($table === 'sylius_address_log_entries') {
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s TYPE JSONB USING %s::jsonb', $table, $dataColumn, $dataColumn));
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s DROP NOT NULL', $table, $dataColumn));
            $this->addSql(sprintf('COMMENT ON COLUMN %s.%s IS NULL', $table, $dataColumn));
        } else {
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s TYPE JSONB USING %s::jsonb', $table, $dataColumn, $dataColumn));
            $this->addSql(sprintf('COMMENT ON COLUMN %s.%s IS NULL', $table, $dataColumn));
        }

        $connection = $this->connection;
        $rows = $connection->fetchAllAssociative(sprintf('SELECT %s, %s FROM %s', 'id', $dataColumn, $table));

        foreach ($rows as $row) {
            $id = $row['id'];
            $data = $row[$dataColumn];

            $this->skipIf(@unserialize($data) === false, sprintf('Data in %s is not serialized', $table));
            $encodedData = unserialize($data);
            $encodedData = json_encode($encodedData);

            $connection->UPDATE($table, [$dataColumn => $encodedData], ['id' => $id]);
        }
    }

    private function changeTypesFromJsonToText(string $table, string $dataColumn): void
    {
        if ($table === 'sylius_address_log_entries') {
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s TYPE TEXT', $table, $dataColumn));
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s SET NOT NULL', $table, $dataColumn));
            $this->addSql(sprintf('COMMENT ON COLUMN %s.%s IS \'(DC2Type:array)\'', $table, $dataColumn));
        } else {
            $this->addSql(sprintf('ALTER TABLE %s ALTER %s TYPE TEXT', $table, $dataColumn));
            $this->addSql(sprintf('COMMENT ON COLUMN %s.%s IS \'(DC2Type:array)\'', $table, $dataColumn));
        }
    }

    private function serializeEncodedData(string $table, string $dataColumn): void
    {
        $connection = $this->connection;
        $rows = $connection->fetchAllAssociative(sprintf('SELECT %s, %s FROM %s', 'id', $dataColumn, $table));

        foreach ($rows as $row) {
            $id = $row['id'];
            $data = $row[$dataColumn];

            $this->skipIf(@json_decode($data) === false, sprintf('Data in %s is not json', $table));
            $decodedData = json_decode($data, true);
            $decodedData = serialize($decodedData);

            $connection->UPDATE($table, [$dataColumn => $decodedData], ['id' => $id]);
        }
    }

    /**
     * @return iterable<array{string, string}>
     */
    private function tablesAndColumnsToBeUpdated(): iterable
    {
        yield ['sylius_address_log_entries', 'data'];
        yield ['sylius_admin_user', 'roles'];
        yield ['sylius_catalog_promotion_action', 'configuration'];
        yield ['sylius_catalog_promotion_scope', 'configuration'];
        yield ['sylius_product_attribute', 'configuration'];
        yield ['sylius_promotion_action', 'configuration'];
        yield ['sylius_promotion_rule', 'configuration'];
        yield ['sylius_shipping_method', 'configuration'];
        yield ['sylius_shipping_method_rule', 'configuration'];
        yield ['sylius_shop_user', 'roles'];
    }
}

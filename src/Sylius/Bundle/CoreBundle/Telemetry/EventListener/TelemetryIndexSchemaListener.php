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

namespace Sylius\Bundle\CoreBundle\Telemetry\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Index;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

/** @internal */
final class TelemetryIndexSchemaListener
{
    private const TABLE_NAME = 'sylius_order';

    private const INDEX_NAME = 'IDX_TELEMETRY_ORDER_STATS';

    public function __construct(private Connection $connection)
    {
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        if (!$schema->hasTable(self::TABLE_NAME)) {
            return;
        }

        $dbIndex = $this->findIndexInDatabase();

        if ($dbIndex === null) {
            return;
        }

        $table = $schema->getTable(self::TABLE_NAME);

        if ($table->hasIndex(self::INDEX_NAME)) {
            return;
        }

        $table->addIndex(
            $dbIndex->getColumns(),
            $dbIndex->getName(),
            $dbIndex->getFlags(),
            $dbIndex->getOptions(),
        );
    }

    private function findIndexInDatabase(): ?Index
    {
        try {
            $dbIndexes = $this->connection->createSchemaManager()->listTableIndexes(self::TABLE_NAME);
        } catch (\Throwable) {
            return null;
        }

        $dbIndexes = array_change_key_case($dbIndexes);

        return $dbIndexes[strtolower(self::INDEX_NAME)] ?? null;
    }
}

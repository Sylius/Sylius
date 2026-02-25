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

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
            $dbIndex->getOptions()
        );
    }

    private function findIndexInDatabase(): ?Index
    {
        try {
            $dbIndexes = $this->connection->getSchemaManager()->listTableIndexes(self::TABLE_NAME);
        } catch (\Throwable $e) {
            return null;
        }

        $dbIndexes = array_change_key_case($dbIndexes);

        return isset($dbIndexes[strtolower(self::INDEX_NAME)]) ? $dbIndexes[strtolower(self::INDEX_NAME)] : null;
    }
}

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

namespace Sylius\Bundle\CoreBundle\Telemetry\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** @internal */
final class TimeoutRunner
{
    public function __construct(
        private int $timeoutMs = 60000,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    public function fetchAllAssociative(Connection $connection, string $sql, array $params = []): array
    {
        $sql = $this->applyTimeout($connection, $sql);

        return $this->executeInTimeoutContext($connection, fn () => $connection->fetchAllAssociative($sql, $params));
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>|false
     */
    public function fetchAssociative(Connection $connection, string $sql, array $params = [])
    {
        $sql = $this->applyTimeout($connection, $sql);

        return $this->executeInTimeoutContext($connection, fn () => $connection->fetchAssociative($sql, $params));
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return list<mixed>
     */
    public function fetchFirstColumn(Connection $connection, string $sql, array $params = []): array
    {
        $sql = $this->applyTimeout($connection, $sql);

        return $this->executeInTimeoutContext($connection, fn () => $connection->fetchFirstColumn($sql, $params));
    }

    private function applyTimeout(Connection $connection, string $sql): string
    {
        $platform = $connection->getDatabasePlatform();

        // MariaDB: SET STATEMENT max_statement_time=X FOR SELECT ... (per-statement, seconds)
        if ($platform instanceof MariaDBPlatform) {
            $timeoutSeconds = $this->timeoutMs / 1000;

            return sprintf('SET STATEMENT max_statement_time=%s FOR %s', $timeoutSeconds, $sql);
        }

        // MySQL: SELECT /*+ MAX_EXECUTION_TIME(X) */ ... (optimizer hint, milliseconds)
        if ($platform instanceof AbstractMySQLPlatform) {
            return preg_replace(
                '/^\s*SELECT\b/i',
                sprintf('SELECT /*+ MAX_EXECUTION_TIME(%d) */', $this->timeoutMs),
                $sql,
                1,
            );
        }

        // PostgreSQL: no per-query mechanism, use SET LOCAL in transaction
        // Handled in executeInTimeoutContext()
        return $sql;
    }

    /**
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
     */
    private function executeInTimeoutContext(Connection $connection, callable $callback)
    {
        $platform = $connection->getDatabasePlatform();

        // PostgreSQL: SET LOCAL only works within a transaction and resets automatically on COMMIT/ROLLBACK
        if ($platform instanceof PostgreSQLPlatform) {
            $connection->beginTransaction();

            try {
                $connection->executeStatement(sprintf("SET LOCAL statement_timeout = '%d'", $this->timeoutMs));
                $result = $callback();
                $connection->commit();

                return $result;
            } catch (\Throwable $e) {
                $connection->rollBack();

                throw $e;
            }
        }

        // MySQL/MariaDB: timeout is embedded in the SQL itself, just execute
        return $callback();
    }
}

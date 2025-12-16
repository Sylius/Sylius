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

namespace Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\DatabaseData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class DatabasePlatformDataProvider implements DataProviderInterface
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function provide(): TelemetryDataInterface
    {
        $connection = $this->getConnection();
        if (null === $connection) {
            return new DatabaseData(type: null, version: null);
        }

        $databaseType = $this->guessDatabaseType($connection);

        return new DatabaseData(
            type: $databaseType,
            version: $this->detectDatabaseVersion($connection, $databaseType),
        );
    }

    private function getConnection(): ?Connection
    {
        try {
            return $this->managerRegistry->getConnection();
        } catch (\Throwable) {
            return null;
        }
    }

    private function guessDatabaseType(Connection $connection): ?string
    {
        try {
            $platform = $connection->getDatabasePlatform();
            if ($platform instanceof AbstractPlatform) {
                return $platform->getName();
            }
        } catch (\Throwable) {
        }

        $params = $connection->getParams();
        if (isset($params['driver'])) {
            return $this->mapDriverToDatabase((string) $params['driver']);
        }

        return null;
    }

    private function mapDriverToDatabase(string $driver): ?string
    {
        return match (true) {
            str_contains($driver, 'mysql') || str_contains($driver, 'mysqli') => 'mysql',
            str_contains($driver, 'pgsql') || str_contains($driver, 'postgres') => 'postgresql',
            str_contains($driver, 'sqlite') => 'sqlite',
            str_contains($driver, 'sqlsrv') || str_contains($driver, 'mssql') => 'mssql',
            str_contains($driver, 'oci') || str_contains($driver, 'oracle') => 'oracle',
            default => null,
        };
    }

    private function detectDatabaseVersion(Connection $connection, ?string $databaseType): ?string
    {
        $params = $connection->getParams();
        $serverVersion = $params['serverVersion'] ?? null;
        if (is_string($serverVersion) && '' !== trim($serverVersion)) {
            return $serverVersion;
        }

        foreach ($this->getVersionQueries($databaseType) as $query) {
            try {
                $result = $connection->fetchOne($query);
                if (is_string($result) && '' !== trim($result)) {
                    return $result;
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function getVersionQueries(?string $databaseType): array
    {
        return match ($databaseType) {
            'postgresql' => [
                'SHOW server_version',
                "SELECT current_setting('server_version')",
                'SELECT VERSION()',
            ],
            'mysql' => [
                'SELECT VERSION()',
            ],
            'sqlite' => [
                'SELECT sqlite_version()',
            ],
            'mssql' => [
                'SELECT @@VERSION',
                "SELECT SERVERPROPERTY('ProductVersion')",
            ],
            'oracle' => [
                "SELECT BANNER FROM v\$version WHERE BANNER LIKE 'Oracle%'",
                "SELECT version FROM v\$instance",
                "SELECT * FROM PRODUCT_COMPONENT_VERSION WHERE PRODUCT LIKE 'Oracle%'",
            ],
            default => [
                'SELECT VERSION()',
            ],
        };
    }
}

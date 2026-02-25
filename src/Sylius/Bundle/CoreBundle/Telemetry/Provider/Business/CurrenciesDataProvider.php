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

namespace Sylius\Bundle\CoreBundle\Telemetry\Provider\Business;

use Doctrine\DBAL\Connection;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\CurrenciesData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class CurrenciesDataProvider implements DataProviderInterface
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $currencies = $this->connection->fetchFirstColumn('SELECT code FROM sylius_currency');

            return new CurrenciesData($currencies);
        } catch (\Throwable $e) {
            return new CurrenciesData([]);
        }
    }
}

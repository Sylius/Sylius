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
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class CurrenciesDataProvider implements DataProviderInterface
{
    public function __construct(
        private Connection $connection,
        private TimeoutRunner $queryTimeoutRunner,
    ) {
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $currencies = $this->queryTimeoutRunner->fetchFirstColumn(
                $this->connection,
                'SELECT code FROM sylius_currency',
            );

            return new CurrenciesData($currencies);
        } catch (\Throwable) {
            return new CurrenciesData([]);
        }
    }
}

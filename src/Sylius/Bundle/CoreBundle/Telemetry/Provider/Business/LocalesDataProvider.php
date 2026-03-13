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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\LocalesData;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class LocalesDataProvider implements DataProviderInterface
{
    public function __construct(
        private Connection $connection,
        private TimeoutRunner $queryTimeoutRunner,
        private string $defaultLocale,
    ) {
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $locales = $this->queryTimeoutRunner->fetchFirstColumn(
                $this->connection,
                'SELECT code FROM sylius_locale',
            );
            $channelDefaultLocales = $this->queryTimeoutRunner->fetchFirstColumn(
                $this->connection,
                'SELECT DISTINCT l.code FROM sylius_locale l
                 INNER JOIN sylius_channel c ON c.default_locale_id = l.id
                 WHERE c.enabled = true',
            );

            return new LocalesData($locales, $channelDefaultLocales, $this->defaultLocale);
        } catch (\Throwable) {
            return new LocalesData([], [], $this->defaultLocale);
        }
    }
}

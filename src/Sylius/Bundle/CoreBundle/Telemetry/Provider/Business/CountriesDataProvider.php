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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\CountriesData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class CountriesDataProvider implements DataProviderInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $channelsWithCountries = $this->connection->fetchAllAssociative(
                'SELECT c.id as channel_id, co.code as country_code
                 FROM sylius_channel c
                 LEFT JOIN sylius_channel_countries cc ON cc.channel_id = c.id
                 LEFT JOIN sylius_country co ON co.id = cc.country_id AND co.enabled = 1',
            );

            $allCountries = $this->connection->fetchFirstColumn(
                'SELECT code FROM sylius_country WHERE enabled = 1',
            );

            $countriesByChannel = [];
            $channelIds = [];

            foreach ($channelsWithCountries as $row) {
                $channelId = $row['channel_id'];
                $channelIds[$channelId] = true;

                if ($row['country_code'] !== null) {
                    $countriesByChannel[$channelId][] = $row['country_code'];
                }
            }

            $countries = [];
            foreach (array_keys($channelIds) as $channelId) {
                $channelCountries = $countriesByChannel[$channelId] ?? $allCountries;
                foreach ($channelCountries as $country) {
                    $countries[$country] = true;
                }
            }

            return new CountriesData(array_keys($countries));
        } catch (\Throwable) {
            return new CountriesData([]);
        }
    }
}

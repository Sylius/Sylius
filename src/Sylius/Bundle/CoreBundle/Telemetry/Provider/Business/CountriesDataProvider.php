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
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class CountriesDataProvider implements DataProviderInterface
{
    /** @var Connection */
    private $connection;

    /** @var TimeoutRunner */
    private $queryTimeoutRunner;

    public function __construct(Connection $connection, TimeoutRunner $queryTimeoutRunner)
    {
        $this->connection = $connection;
        $this->queryTimeoutRunner = $queryTimeoutRunner;
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $channelsWithCountries = $this->queryTimeoutRunner->fetchAllAssociative(
                $this->connection,
                'SELECT c.id as channel_id, co.code as country_code
                 FROM sylius_channel c
                 LEFT JOIN sylius_channel_countries cc ON cc.channel_id = c.id
                 LEFT JOIN sylius_country co ON co.id = cc.country_id AND co.enabled = true
                 WHERE c.enabled = true'
            );

            $allCountries = $this->queryTimeoutRunner->fetchFirstColumn(
                $this->connection,
                'SELECT code FROM sylius_country WHERE enabled = true'
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
                $channelCountries = isset($countriesByChannel[$channelId]) ? $countriesByChannel[$channelId] : $allCountries;
                foreach ($channelCountries as $country) {
                    $countries[$country] = true;
                }
            }

            return new CountriesData(array_keys($countries));
        } catch (\Throwable $e) {
            return new CountriesData([]);
        }
    }
}

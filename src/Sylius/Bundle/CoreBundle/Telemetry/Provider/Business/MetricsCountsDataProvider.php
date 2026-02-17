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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\MetricsCountsData;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class MetricsCountsDataProvider implements DataProviderInterface
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
            $counts = $this->connection->fetchAssociative(
                'SELECT
                    (SELECT COUNT(id) FROM sylius_customer) as customers_count,
                    (SELECT COUNT(id) FROM sylius_product) as products_count,
                    (SELECT COUNT(id) FROM sylius_product_variant) as product_variants_count,
                    (SELECT COUNT(id) FROM sylius_product_variant WHERE shipping_required = 0) as virtual_product_variants_count,
                    (SELECT COUNT(id) FROM sylius_order WHERE checkout_state = :completedState) as orders_count,
                    (SELECT COUNT(id) FROM sylius_channel WHERE enabled = 1) as channels_count',
                ['completedState' => OrderCheckoutStates::STATE_COMPLETED]
            );

            return new MetricsCountsData(
                ValueRangeMapper::mapCustomersCount((int) $counts['customers_count']),
                ValueRangeMapper::mapProductsCount((int) $counts['products_count']),
                ValueRangeMapper::mapVariantsCount((int) $counts['product_variants_count']),
                ValueRangeMapper::mapVirtualVariantsCount((int) $counts['virtual_product_variants_count']),
                (int) $counts['orders_count'],
                (int) $counts['channels_count']
            );
        } catch (\Throwable $e) {
            return new MetricsCountsData(
                ValueRangeMapper::mapCustomersCount(0),
                ValueRangeMapper::mapProductsCount(0),
                ValueRangeMapper::mapVariantsCount(0),
                ValueRangeMapper::mapVirtualVariantsCount(0),
                0,
                0
            );
        }
    }
}

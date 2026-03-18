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
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class MetricsCountsDataProvider implements DataProviderInterface
{
    public function __construct(
        private Connection $connection,
        private TimeoutRunner $queryTimeoutRunner,
    ) {
    }

    public function provide(): TelemetryDataInterface
    {
        try {
            $counts = $this->queryTimeoutRunner->fetchAssociative(
                $this->connection,
                'SELECT
                    (SELECT COUNT(id) FROM sylius_customer) as customers_count,
                    (SELECT COUNT(id) FROM sylius_product) as products_count,
                    (SELECT COUNT(id) FROM sylius_product_variant) as product_variants_count,
                    (SELECT COUNT(id) FROM sylius_product_variant WHERE shipping_required = false) as virtual_product_variants_count,
                    (SELECT COUNT(id) FROM sylius_order WHERE checkout_state = :completedState) as orders_count,
                    (SELECT COUNT(id) FROM sylius_channel WHERE enabled = true) as channels_count',
                [
                    'completedState' => OrderCheckoutStates::STATE_COMPLETED,
                ],
            );

            return new MetricsCountsData(
                customersCount: ValueRangeMapper::mapCustomersCount((int) $counts['customers_count']),
                productsCount: ValueRangeMapper::mapProductsCount((int) $counts['products_count']),
                productVariantsCount: ValueRangeMapper::mapVariantsCount((int) $counts['product_variants_count']),
                virtualProductVariantsCount: ValueRangeMapper::mapVirtualVariantsCount((int) $counts['virtual_product_variants_count']),
                ordersCount: (int) $counts['orders_count'],
                channelsCount: (int) $counts['channels_count'],
            );
        } catch (\Throwable) {
            return new MetricsCountsData(
                customersCount: ValueRangeMapper::mapCustomersCount(0),
                productsCount: ValueRangeMapper::mapProductsCount(0),
                productVariantsCount: ValueRangeMapper::mapVariantsCount(0),
                virtualProductVariantsCount: ValueRangeMapper::mapVirtualVariantsCount(0),
                ordersCount: 0,
                channelsCount: 0,
            );
        }
    }
}

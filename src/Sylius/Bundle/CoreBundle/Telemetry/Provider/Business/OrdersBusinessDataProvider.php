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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\OrderMetricsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\OrdersBusinessData;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class OrdersBusinessDataProvider implements DataProviderInterface
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
            $results = $this->fetchOrdersData();
            $processedData = $this->processResults($results);

            return $this->buildDto($processedData);
        } catch (\Throwable $e) {
            return $this->getEmptyDto();
        }
    }

    /** @return list<array<string, mixed>> */
    private function fetchOrdersData(): array
    {
        $oneMonthAgo = (new \DateTimeImmutable('-1 month'))->format('Y-m-d H:i:s');

        return $this->connection->fetchAllAssociative(
            'SELECT
                o.currency_code,
                COUNT(o.id) as order_count,
                SUM(o.total) as gmv,
                COALESCE(AVG(item_data.item_count), 0) as avg_items,
                COALESCE(AVG(item_data.unit_count), 0) as avg_units
            FROM sylius_order o
            LEFT JOIN (
                SELECT
                    oi.order_id,
                    COUNT(oi.id) as item_count,
                    SUM(oi.quantity) as unit_count
                FROM sylius_order_item oi
                INNER JOIN sylius_order o2
                    ON oi.order_id = o2.id
                    AND o2.checkout_completed_at >= :oneMonthAgo
                    AND o2.checkout_state = :completedState
                    AND o2.payment_state != :cancelledState
                GROUP BY oi.order_id
            ) as item_data ON o.id = item_data.order_id
            WHERE o.checkout_completed_at >= :oneMonthAgo
                AND o.checkout_state = :completedState
                AND o.payment_state != :cancelledState
            GROUP BY o.currency_code',
            [
                'oneMonthAgo' => $oneMonthAgo,
                'completedState' => OrderCheckoutStates::STATE_COMPLETED,
                'cancelledState' => OrderPaymentStates::STATE_CANCELLED,
            ]
        );
    }

    /**
     * @param list<array<string, mixed>> $results
     * @return array<string, mixed>
     */
    private function processResults(array $results): array
    {
        $gmvByCurrency = [];
        $aovByCurrency = [];
        $totalOrders = 0;
        $totalAvgItems = 0;
        $totalAvgUnits = 0;
        $currencyCount = 0;

        foreach ($results as $row) {
            $currency = $row['currency_code'];
            $gmvInMinorUnits = (int) $row['gmv'];
            $orderCount = (int) $row['order_count'];

            $gmv = (int) round($gmvInMinorUnits / 100);
            $aov = $orderCount > 0 ? (int) round($gmvInMinorUnits / $orderCount / 100) : 0;

            $gmvByCurrency[$currency] = ValueRangeMapper::mapGmv($gmv);
            $aovByCurrency[$currency] = ValueRangeMapper::mapAov($aov);

            $totalOrders += $orderCount;
            $totalAvgItems += (float) $row['avg_items'];
            $totalAvgUnits += (float) $row['avg_units'];
            ++$currencyCount;
        }

        return [
            'gmv_by_currency' => $gmvByCurrency,
            'aov_by_currency' => $aovByCurrency,
            'total_orders' => $totalOrders,
            'avg_items' => $currencyCount > 0 ? $totalAvgItems / $currencyCount : 0,
            'avg_units' => $currencyCount > 0 ? $totalAvgUnits / $currencyCount : 0,
        ];
    }

    /** @param array<string, mixed> $processedData */
    private function buildDto(array $processedData): OrdersBusinessData
    {
        return new OrdersBusinessData(
            $processedData['gmv_by_currency'],
            $processedData['aov_by_currency'],
            new OrderMetricsData(
                ValueRangeMapper::mapOrdersCount($processedData['total_orders']),
                ValueRangeMapper::mapAvgItems($processedData['avg_items']),
                ValueRangeMapper::mapAvgItems($processedData['avg_units'])
            )
        );
    }

    private function getEmptyDto(): OrdersBusinessData
    {
        return new OrdersBusinessData(
            [],
            [],
            new OrderMetricsData(
                ValueRangeMapper::mapOrdersCount(0),
                ValueRangeMapper::mapAvgItems(0),
                ValueRangeMapper::mapAvgItems(0)
            )
        );
    }
}

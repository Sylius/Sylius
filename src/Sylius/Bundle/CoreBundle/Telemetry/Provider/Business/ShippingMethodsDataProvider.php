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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\ShippingMethodsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\ShippingProviderData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class ShippingMethodsDataProvider implements DataProviderInterface
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
            $oneMonthAgo = (new \DateTimeImmutable('-1 month'))->format('Y-m-d H:i:s');

            $results = $this->connection->fetchAllAssociative(
                'SELECT sm.code, sm.calculator, COUNT(s.id) as shipments_count
                 FROM sylius_shipping_method sm
                 LEFT JOIN (
                    SELECT s.id, s.method_id
                    FROM sylius_shipment s
                    INNER JOIN sylius_order o ON s.order_id = o.id
                    WHERE o.checkout_completed_at >= :oneMonthAgo
                 ) s ON s.method_id = sm.id
                 WHERE sm.is_enabled = :enabled
                 GROUP BY sm.id, sm.code, sm.calculator',
                ['enabled' => true, 'oneMonthAgo' => $oneMonthAgo]
            );

            $providers = [];
            foreach ($results as $row) {
                $providers[] = new ShippingProviderData(
                    $row['code'],
                    $row['calculator'] ?? '',
                    ValueRangeMapper::mapShipmentsCount((int) $row['shipments_count'])
                );
            }

            return new ShippingMethodsData(...$providers);
        } catch (\Throwable $e) {
            return new ShippingMethodsData();
        }
    }
}

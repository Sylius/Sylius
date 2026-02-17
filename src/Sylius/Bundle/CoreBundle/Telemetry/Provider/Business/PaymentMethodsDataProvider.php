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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\PaymentMethodsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\PaymentProviderData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class PaymentMethodsDataProvider implements DataProviderInterface
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
                'SELECT pm.code, gc.factory_name, COUNT(p.id) as payments_count
                 FROM sylius_payment_method pm
                 JOIN sylius_gateway_config gc ON pm.gateway_config_id = gc.id
                 LEFT JOIN (
                    SELECT p.id, p.method_id
                    FROM sylius_payment p
                    INNER JOIN sylius_order o ON p.order_id = o.id
                    WHERE o.checkout_completed_at >= :oneMonthAgo
                 ) p ON p.method_id = pm.id
                 WHERE pm.is_enabled = :enabled
                 GROUP BY pm.id, pm.code, gc.factory_name',
                ['enabled' => true, 'oneMonthAgo' => $oneMonthAgo]
            );

            $providers = [];
            foreach ($results as $row) {
                $providers[] = new PaymentProviderData(
                    $row['code'],
                    $row['factory_name'] ?? '',
                    ValueRangeMapper::mapPaymentsCount((int) $row['payments_count'])
                );
            }

            return new PaymentMethodsData(...$providers);
        } catch (\Throwable $e) {
            return new PaymentMethodsData();
        }
    }
}

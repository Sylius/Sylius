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
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

/** @internal */
final class PaymentMethodsDataProvider implements DataProviderInterface
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
            $results = $this->queryTimeoutRunner->fetchAllAssociative(
                $this->connection,
                'SELECT pm.code, gc.factory_name, pm.is_enabled, COUNT(p.id) as payments_count
                 FROM sylius_payment_method pm
                 JOIN sylius_gateway_config gc ON pm.gateway_config_id = gc.id
                 LEFT JOIN sylius_payment p ON p.method_id = pm.id
                 WHERE EXISTS (SELECT 1 FROM sylius_payment_method_channels pmc WHERE pmc.payment_method_id = pm.id)
                 GROUP BY pm.id, pm.code, gc.factory_name, pm.is_enabled'
            );

            $providers = [];
            foreach ($results as $row) {
                $providers[] = new PaymentProviderData(
                    $row['code'],
                    $row['factory_name'] ?? '',
                    ValueRangeMapper::mapPaymentsCount((int) $row['payments_count']),
                    (bool) $row['is_enabled']
                );
            }

            return new PaymentMethodsData(...$providers);
        } catch (\Throwable $e) {
            return new PaymentMethodsData();
        }
    }
}

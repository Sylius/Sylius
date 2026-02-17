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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Business;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class OrderMetricsData implements TelemetryDataInterface
{
    /** @var string */
    public $ordersMonthlyCount;

    /** @var string */
    public $ordersMonthlyAvgItems;

    /** @var string */
    public $ordersMonthlyAvgItemUnits;

    public function __construct(
        string $ordersMonthlyCount,
        string $ordersMonthlyAvgItems,
        string $ordersMonthlyAvgItemUnits
    ) {
        $this->ordersMonthlyCount = $ordersMonthlyCount;
        $this->ordersMonthlyAvgItems = $ordersMonthlyAvgItems;
        $this->ordersMonthlyAvgItemUnits = $ordersMonthlyAvgItemUnits;
    }

    /** @return array<string, string> */
    public function normalize(): array
    {
        return [
            'orders_monthly_count' => $this->ordersMonthlyCount,
            'orders_monthly_avg_items' => $this->ordersMonthlyAvgItems,
            'orders_monthly_avg_item_units' => $this->ordersMonthlyAvgItemUnits,
        ];
    }
}

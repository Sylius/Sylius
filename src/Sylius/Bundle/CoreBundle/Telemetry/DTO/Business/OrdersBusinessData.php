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
final class OrdersBusinessData implements TelemetryDataInterface
{
    /**
     * @param array<string, string> $gmvMonthly
     * @param array<string, string> $aovMonthly
     */
    public function __construct(
        public array $gmvMonthly,
        public array $aovMonthly,
        public OrderMetricsData $metrics,
    ) {
    }

    public function normalize(): array
    {
        return [
            'gmv_monthly' => $this->gmvMonthly,
            'aov_monthly' => $this->aovMonthly,
            'order_metrics' => $this->metrics->normalize(),
        ];
    }
}

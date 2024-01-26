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

namespace Sylius\Component\Core\Statistics\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

class StatisticsProvider implements StatisticsProviderInterface
{
    public function __construct(
        private SalesTimeSeriesProviderInterface $salesTimeSeriesProvider,
        private BusinessActivitySummaryProviderInterface $businessActivitySummaryProvider,
    ) {
    }

    public function provide(\DatePeriod $datePeriod, ChannelInterface $channel): Statistics
    {
        $salesTimeSeries = $this->salesTimeSeriesProvider->provide($datePeriod, $channel);
        $businessActivitySummary = $this->businessActivitySummaryProvider->provide($datePeriod, $channel);

        return new Statistics($salesTimeSeries, $businessActivitySummary);
    }
}

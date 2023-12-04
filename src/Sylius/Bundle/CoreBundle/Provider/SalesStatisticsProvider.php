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

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\ValueObject\SalesStatistics;

class SalesStatisticsProvider implements SalesStatisticsProviderInterface
{
    public function __construct(
        private DashboardStatisticsProviderInterface $statisticsProvider,
        private SalesDataProviderInterface $salesDataProvider,
    ) {
    }

    public function provide(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        Interval $interval,
    ): SalesStatistics {
        $statistics = $this->statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate);

        $salesSummary = $this->salesDataProvider->getSalesSummary(
            $channel,
            $startDate,
            $endDate,
            $interval,
            true,
        );

        return new SalesStatistics(
            $salesSummary,
            $interval,
            $statistics->getNumberOfNewCustomers(),
            $statistics->getNumberOfNewOrders(),
            $statistics->getTotalSales(),
            $statistics->getAverageOrderValue(),
        );
    }
}

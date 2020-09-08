<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class StatisticsDataProvider implements StatisticsDataProviderInterface
{
    /** @var DashboardStatisticsProviderInterface */
    private $statisticsProvider;

    /** @var SalesDataProviderInterface */
    private $salesDataProvider;

    /** @var MoneyFormatterInterface */
    private $moneyFormatter;

    public function __construct(
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        MoneyFormatterInterface $moneyFormatter
    ) {
        $this->statisticsProvider = $statisticsProvider;
        $this->salesDataProvider = $salesDataProvider;
        $this->moneyFormatter = $moneyFormatter;
    }

    public function getRawData(ChannelInterface $channel, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $interval): array
    {
        /** @var DashboardStatisticsInterface $statistics */
        $statistics = $this->statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate);

        $salesSummary = $this->salesDataProvider->getSalesSummary(
            $channel,
            $startDate,
            $endDate,
            Interval::{$interval}()
        );

        /** @var string $currencyCode */
        $currencyCode = $channel->getBaseCurrency()->getCode();

        return [
            'sales_summary' => [
                'intervals' => $salesSummary->getIntervals(),
                'sales' => $salesSummary->getSales(),
            ],
            'channel' => [
                'base_currency_code' => $currencyCode,
                'channel_code' => $channel->getCode(),
            ],
            'statistics' => [
                'total_sales' => $this->moneyFormatter->format($statistics->getTotalSales(), $currencyCode),
                'number_of_new_orders' => $statistics->getNumberOfNewOrders(),
                'number_of_new_customers' => $statistics->getNumberOfNewCustomers(),
                'average_order_value' => $this->moneyFormatter->format($statistics->getAverageOrderValue(), $currencyCode),
            ],
        ];
    }
}

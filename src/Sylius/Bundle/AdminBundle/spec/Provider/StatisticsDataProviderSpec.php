<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProvider;
use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Dashboard\DashboardStatistics;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Dashboard\SalesSummaryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class StatisticsDataProviderSpec extends ObjectBehavior
{
    function let(
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        MoneyFormatterInterface $moneyFormatter
    ): void {
        $this->beConstructedWith($statisticsProvider, $salesDataProvider, $moneyFormatter);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(StatisticsDataProvider::class);
    }

    function it_implements_a_statistics_data_provider_interface(): void
    {
        $this->shouldImplement(StatisticsDataProviderInterface::class);
    }

    function it_provides_data(
        ChannelInterface $channel,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        DashboardStatistics $statistics,
        SalesSummaryInterface $salesSummary,
        CurrencyInterface $currency,
        MoneyFormatterInterface $moneyFormatter
    ): void {
        $startDate = new \DateTime('2020-01-01');
        $endDate = new \DateTime('2021-01-01');

        $statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate)->willReturn($statistics);

        $statistics->getTotalSales()->willReturn(37274);
        $statistics->getAverageOrderValue()->willReturn(18637);
        $statistics->getNumberOfNewOrders()->willReturn(2);
        $statistics->getNumberOfNewCustomers()->willReturn(21);

        $salesDataProvider->getSalesSummary($channel, $startDate, $endDate, Interval::{'month'}())->willReturn($salesSummary);

        $salesSummary->getIntervals()->willReturn(['1.2020', '2.2020', '3.2020', '4.2020', '5.2020', '6.2020', '7.2020', '8.2020', '9.2020', '10.2020', '11.2020', '12.2020']);
        $salesSummary->getSales()->willReturn(['0', '168.82', '0', '203.92', '0', '0', '0', '0', '0', '0', '0', '0']);
        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('USD');

        $moneyFormatter->format(37274, 'USD')->willReturn('$372.74');

        $moneyFormatter->format(18637, 'USD')->willReturn('$186.37');

        $channel->getCode()->willReturn('FASHION_WEB');

        $this->getRawData($channel, $startDate, $endDate, 'month')->shouldReturn([
            'sales_summary' => [
                'months' => ['1.2020', '2.2020', '3.2020', '4.2020', '5.2020', '6.2020', '7.2020', '8.2020', '9.2020', '10.2020', '11.2020', '12.2020'],
                'sales' => ['0', '168.82', '0', '203.92', '0', '0', '0', '0', '0', '0', '0', '0']
            ],
            'channel' => [
                'base_currency_code' => 'USD',
                'channel_code' => 'FASHION_WEB',
            ],
            'statistics'=>[
                'total_sales' => '$372.74',
                'number_of_new_orders' => 2,
                'number_of_new_customers' => 21,
                'average_order_value' => '$186.37',
            ]
        ]);
    }
}

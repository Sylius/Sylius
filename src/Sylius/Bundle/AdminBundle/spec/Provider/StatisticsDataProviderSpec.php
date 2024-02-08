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

final class StatisticsDataProviderSpec extends ObjectBehavior
{
    function let(
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        MoneyFormatterInterface $moneyFormatter,
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

    function it_provides_data_for_year(
        ChannelInterface $channel,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        DashboardStatistics $statistics,
        SalesSummaryInterface $salesSummary,
        CurrencyInterface $currency,
        MoneyFormatterInterface $moneyFormatter,
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
                'intervals' => ['1.2020', '2.2020', '3.2020', '4.2020', '5.2020', '6.2020', '7.2020', '8.2020', '9.2020', '10.2020', '11.2020', '12.2020'],
                'sales' => ['0', '168.82', '0', '203.92', '0', '0', '0', '0', '0', '0', '0', '0'],
            ],
            'channel' => [
                'base_currency_code' => 'USD',
                'channel_code' => 'FASHION_WEB',
            ],
            'statistics' => [
                'total_sales' => '$372.74',
                'number_of_new_orders' => 2,
                'number_of_new_customers' => 21,
                'average_order_value' => '$186.37',
            ],
        ]);
    }

    function it_provides_data_for_month(
        ChannelInterface $channel,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        DashboardStatistics $statistics,
        SalesSummaryInterface $salesSummary,
        CurrencyInterface $currency,
        MoneyFormatterInterface $moneyFormatter,
    ): void {
        $startDate = new \DateTime('2020-05-01');
        $endDate = new \DateTime('2020-06-01');

        $statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate)->willReturn($statistics);

        $statistics->getTotalSales()->willReturn(51974);
        $statistics->getAverageOrderValue()->willReturn(25987);
        $statistics->getNumberOfNewOrders()->willReturn(2);
        $statistics->getNumberOfNewCustomers()->willReturn(9);

        $salesDataProvider->getSalesSummary($channel, $startDate, $endDate, Interval::{'day'}())->willReturn($salesSummary);

        $salesSummary->getIntervals()->willReturn(
            [
                '1.5.2020', '2.5.2020', '3.5.2020', '4.5.2020', '5.5.2020',
                '6.5.2020', '7.5.2020', '8.5.2020', '9.5.2020', '10.5.2020',
                '11.5.2020', '12.5.2020', '13.5.2020', '14.5.2020', '15.5.2020',
                '16.5.2020', '17.5.2020', '18.5.2020', '19.5.2020', '20.5.2020',
                '21.5.2020', '22.5.2020', '23.5.2020', '24.5.2020', '25.5.2020',
                '26.5.2020', '27.5.2020', '28.5.2020', '29.5.2020', '30.5.2020',
                '31.5.2020',
            ],
        );
        $salesSummary->getSales()->willReturn(
            [
                '0', '168.82', '0', '203.92', '0',
                '0', '0', '40.50', '0', '0', '23.0', '0',
                '0', '0', '0', '0', '0', '0', '0',
                '0', '0', '0', '81.0', '0', '0', '0',
                '0', '0', '0', '0', '0', '4.50', '0',
            ],
        );
        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('USD');

        $moneyFormatter->format(51974, 'USD')->willReturn('$519.74');

        $moneyFormatter->format(25987, 'USD')->willReturn('$259.87');

        $channel->getCode()->willReturn('FASHION_WEB');

        $this->getRawData($channel, $startDate, $endDate, 'day')->shouldReturn([
            'sales_summary' => [
                'intervals' => [
                    '1.5.2020', '2.5.2020', '3.5.2020', '4.5.2020', '5.5.2020',
                    '6.5.2020', '7.5.2020', '8.5.2020', '9.5.2020', '10.5.2020',
                    '11.5.2020', '12.5.2020', '13.5.2020', '14.5.2020', '15.5.2020',
                    '16.5.2020', '17.5.2020', '18.5.2020', '19.5.2020', '20.5.2020',
                    '21.5.2020', '22.5.2020', '23.5.2020', '24.5.2020', '25.5.2020',
                    '26.5.2020', '27.5.2020', '28.5.2020', '29.5.2020', '30.5.2020',
                    '31.5.2020',
                ],
                'sales' => [
                    '0', '168.82', '0', '203.92', '0',
                    '0', '0', '40.50', '0', '0', '23.0', '0',
                    '0', '0', '0', '0', '0', '0', '0',
                    '0', '0', '0', '81.0', '0', '0', '0',
                    '0', '0', '0', '0', '0', '4.50', '0',
                ],
            ],
            'channel' => [
                'base_currency_code' => 'USD',
                'channel_code' => 'FASHION_WEB',
            ],
            'statistics' => [
                'total_sales' => '$519.74',
                'number_of_new_orders' => 2,
                'number_of_new_customers' => 9,
                'average_order_value' => '$259.87',
            ],
        ]);
    }

    function it_provides_data_for_2_weeks(
        ChannelInterface $channel,
        DashboardStatisticsProviderInterface $statisticsProvider,
        SalesDataProviderInterface $salesDataProvider,
        DashboardStatistics $statistics,
        SalesSummaryInterface $salesSummary,
        CurrencyInterface $currency,
        MoneyFormatterInterface $moneyFormatter,
    ): void {
        $startDate = new \DateTime('2020-05-21');
        $endDate = new \DateTime('2020-06-04');

        $statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate)->willReturn($statistics);

        $statistics->getTotalSales()->willReturn(46424);
        $statistics->getAverageOrderValue()->willReturn(11606);
        $statistics->getNumberOfNewOrders()->willReturn(4);
        $statistics->getNumberOfNewCustomers()->willReturn(11);

        $salesDataProvider->getSalesSummary($channel, $startDate, $endDate, Interval::{'day'}())->willReturn($salesSummary);

        $salesSummary->getIntervals()->willReturn(
            [
                '21.5.2020', '22.5.2020', '23.5.2020', '24.5.2020', '25.5.2020', '26.5.2020', '27.5.2020',
                '28.5.2020', '29.5.2020', '30.5.2020', '31.5.2020', '1.6.2020', '2.6.2020', '3.6.2020',
            ],
        );
        $salesSummary->getSales()->willReturn(
            [
                '0', '168.82', '0', '203.92', '0', '0', '10.0',
                '0', '0', '40.50', '0', '0', '23.0', '0', '7.0', '11.0',
            ],
        );
        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('USD');

        $moneyFormatter->format(46424, 'USD')->willReturn('$464.24');

        $moneyFormatter->format(11606, 'USD')->willReturn('$116.06');

        $channel->getCode()->willReturn('FASHION_WEB');

        $this->getRawData($channel, $startDate, $endDate, 'day')->shouldReturn([
            'sales_summary' => [
                'intervals' => [
                    '21.5.2020', '22.5.2020', '23.5.2020', '24.5.2020', '25.5.2020', '26.5.2020', '27.5.2020',
                    '28.5.2020', '29.5.2020', '30.5.2020', '31.5.2020', '1.6.2020', '2.6.2020', '3.6.2020',
                ],
                'sales' => [
                    '0', '168.82', '0', '203.92', '0', '0', '10.0',
                    '0', '0', '40.50', '0', '0', '23.0', '0', '7.0', '11.0',
                ],
            ],
            'channel' => [
                'base_currency_code' => 'USD',
                'channel_code' => 'FASHION_WEB',
            ],
            'statistics' => [
                'total_sales' => '$464.24',
                'number_of_new_orders' => 4,
                'number_of_new_customers' => 11,
                'average_order_value' => '$116.06',
            ],
        ]);
    }
}

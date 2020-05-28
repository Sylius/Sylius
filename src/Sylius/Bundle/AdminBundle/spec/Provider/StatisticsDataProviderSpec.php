<?php

namespace spec\Sylius\Bundle\AdminBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProvider;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Dashboard\DashboardStatistics;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProvider;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

class StatisticsDataProviderSpec extends ObjectBehavior
{
    function let(DashboardStatisticsProviderInterface $statisticsProvider, SalesDataProviderInterface $salesDataProvider, MoneyFormatterInterface $moneyFormatter): void
    {
        $this->beConstructedWith($statisticsProvider, $salesDataProvider, $moneyFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StatisticsDataProvider::class);
    }

    function it_provides_data(ChannelInterface $channel, DashboardStatisticsProviderInterface $statisticsProvider, DashboardStatistics $statistics): void
    {
        $startDate = new \DateTime('2020-01-01');
        $endDate = new \DateTime('2021-01-01');
        $interval = 'month';

        $statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate, $interval)->willReturn($statistics);

        $this->getRawData($channel, $startDate, $endDate, $interval)->shouldReturn([]);
    }
}

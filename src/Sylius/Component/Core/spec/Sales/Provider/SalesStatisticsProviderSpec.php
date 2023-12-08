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

namespace spec\Sylius\Component\Core\Statistics\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesPerPeriodProviderInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\Period;
use Sylius\Component\Core\Statistics\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Statistics\ValueObject\SalesSummary;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class StatisticsProviderSpec extends ObjectBehavior
{
    function let(
        SalesPerPeriodProviderInterface $salesPerPeriodProvider,
        BusinessActivitySummaryProviderInterface $salesSummaryProvider,
    ): void {
        $this->beConstructedWith($salesPerPeriodProvider, $salesSummaryProvider);
    }

    function it_implements_sales_statistics_provider_interface(): void
    {
        $this->shouldImplement(StatisticsProviderInterface::class);
    }

    function it_provides_sales_statistics_for_given_period_and_channel(
        SalesPerPeriodProviderInterface $salesPerPeriodProvider,
        BusinessActivitySummaryProviderInterface $salesSummaryProvider,
        ChannelInterface $channel,
        Period $period,
        SalesInPeriod $lastYearSales,
        SalesInPeriod $thisYearSales,
        SalesSummary $salesSummary,
    ): void {
        $startDate = new \DateTimeImmutable('first day of january this year 00:00:00');
        $endDate = new \DateTimeImmutable('last day of december this year 23:59:59');

        $period->getStartDate()->willReturn($startDate);
        $period->getEndDate()->willReturn($endDate);
        $period->getInterval()->willReturn('year');

        $salesPerPeriod = [$lastYearSales->getWrappedObject(), $thisYearSales->getWrappedObject()];
        $salesPerPeriodProvider->provide($period, $channel)->willReturn($salesPerPeriod);

        $salesSummaryProvider->provide($period, $channel)->willReturn($salesSummary);

        $statistics = new Statistics($salesPerPeriod, $salesSummary->getWrappedObject(), $period->getWrappedObject());

        $this->provide($period, $channel)->shouldBeLike($statistics);
    }
}

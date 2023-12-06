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

namespace spec\Sylius\Component\Core\Sales\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Sales\Provider\SalesPerPeriodProviderInterface;
use Sylius\Component\Core\Sales\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Sales\Provider\SalesSummaryProviderInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesStatistics;
use Sylius\Component\Core\Sales\ValueObject\SalesSummary;

final class SalesStatisticsProviderSpec extends ObjectBehavior
{
    function let(
        SalesPerPeriodProviderInterface $salesPerPeriodProvider,
        SalesSummaryProviderInterface $salesSummaryProvider,
    ): void {
        $this->beConstructedWith($salesPerPeriodProvider, $salesSummaryProvider);
    }

    function it_implements_sales_statistics_provider_interface(): void
    {
        $this->shouldImplement(SalesStatisticsProviderInterface::class);
    }

    function it_provides_sales_statistics_for_given_period_and_channel(
        SalesPerPeriodProviderInterface $salesPerPeriodProvider,
        SalesSummaryProviderInterface $salesSummaryProvider,
        ChannelInterface $channel,
        SalesPeriod $salesPeriod,
        SalesInPeriod $lastYearSales,
        SalesInPeriod $thisYearSales,
        SalesSummary $salesSummary,
    ): void {
        $startDate = new \DateTimeImmutable('first day of january this year 00:00:00');
        $endDate = new \DateTimeImmutable('last day of december this year 23:59:59');

        $salesPeriod->getStartDate()->willReturn($startDate);
        $salesPeriod->getEndDate()->willReturn($endDate);
        $salesPeriod->getInterval()->willReturn('year');

        $salesPerPeriod = [$lastYearSales->getWrappedObject(), $thisYearSales->getWrappedObject()];
        $salesPerPeriodProvider->provide($salesPeriod, $channel)->willReturn($salesPerPeriod);

        $salesSummaryProvider->provide($salesPeriod, $channel)->willReturn($salesSummary);

        $salesStatistics = new SalesStatistics($salesPerPeriod, $salesSummary->getWrappedObject(), $salesPeriod->getWrappedObject());

        $this->provide($salesPeriod, $channel)->shouldBeLike($salesStatistics);
    }
}

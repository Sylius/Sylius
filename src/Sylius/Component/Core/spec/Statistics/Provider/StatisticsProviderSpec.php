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
use Sylius\Component\Core\Statistics\Chart\ChartInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesTimeSeriesProviderInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class StatisticsProviderSpec extends ObjectBehavior
{
    function let(
        SalesTimeSeriesProviderInterface $salesTimeSeriesProvider,
        BusinessActivitySummaryProviderInterface $businessActivitySummaryProvider,
    ): void {
        $this->beConstructedWith($salesTimeSeriesProvider, $businessActivitySummaryProvider);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(StatisticsProviderInterface::class);
    }

    function it_provides_statistics(
        SalesTimeSeriesProviderInterface $salesTimeSeriesProvider,
        BusinessActivitySummaryProviderInterface $businessActivitySummaryProvider,
        ChannelInterface $channel,
        \DatePeriod $datePeriod,
        ChartInterface $salesTimeSeries,
        BusinessActivitySummary $businessActivitySummary,
    ): void {
        $salesTimeSeriesProvider->provide($datePeriod, $channel)->willReturn($salesTimeSeries);
        $businessActivitySummaryProvider->provide($datePeriod, $channel)->willReturn($businessActivitySummary);

        $this->provide($datePeriod, $channel)->shouldBeAnInstanceOf(Statistics::class);
    }
}

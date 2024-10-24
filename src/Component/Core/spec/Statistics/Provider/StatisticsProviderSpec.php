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
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class StatisticsProviderSpec extends ObjectBehavior
{
    function let(
        SalesStatisticsProviderInterface $salesProvider,
        BusinessActivitySummaryProviderInterface $businessActivitySummaryProvider,
    ): void {
        $this->beConstructedWith($salesProvider, $businessActivitySummaryProvider);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(StatisticsProviderInterface::class);
    }

    function it_provides_statistics(
        SalesStatisticsProviderInterface $salesProvider,
        BusinessActivitySummaryProviderInterface $businessActivitySummaryProvider,
        ChannelInterface $channel,
        \DatePeriod $datePeriod,
        BusinessActivitySummary $businessActivitySummary,
    ): void {
        $salesProvider->provide('day', $datePeriod, $channel)->willReturn([]);
        $businessActivitySummaryProvider->provide($datePeriod, $channel)->willReturn($businessActivitySummary);

        $this->provide('day', $datePeriod, $channel)->shouldBeAnInstanceOf(Statistics::class);
    }
}

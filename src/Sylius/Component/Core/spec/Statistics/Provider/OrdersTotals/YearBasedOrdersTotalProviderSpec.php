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

namespace spec\Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;

final class YearBasedOrdersTotalProviderSpec extends AbstractOrdersTotalsProviderSpec
{
    protected const DATE_FORMAT = 'Y';

    private const GROUP_SELECT = [
        'year' => 'YEAR(o.checkoutCompletedAt) AS year',
    ];

    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_a_orders_totals_provider(): void
    {
        $this->shouldImplement(OrdersTotalsProviderInterface::class);
    }

    function it_returns_an_array_of_zeros_when_no_totals_have_been_found_for_period(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $start = \DateTimeImmutable::createFromFormat('Y-m', '1999-01');
        $end = \DateTimeImmutable::createFromFormat('Y-m', '1999-12');
        $period = new \DatePeriod($start, new \DateInterval('P1Y'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y', '1999'), 'total' => 0],
        ]);
    }

    function it_returns_an_array_of_totals_per_period(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $start = \DateTimeImmutable::createFromFormat('Y-m', '1999-01');
        $end = \DateTimeImmutable::createFromFormat('Y-m', '2001-12');
        $period = new \DatePeriod($start, new \DateInterval('P1Y'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'total' => 1000],
                ['year' => 2000, 'total' => 2000],
                ['year' => 2001, 'total' => 3000],
            ])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y', '1999'), 'total' => 1000],
            ['period' => \DateTimeImmutable::createFromFormat('Y', '2000'), 'total' => 2000],
            ['period' => \DateTimeImmutable::createFromFormat('Y', '2001'), 'total' => 3000],
        ]);
    }

    function it_returns_fills_zeros_in_periods_with_no_totals(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $start = \DateTimeImmutable::createFromFormat('Y-m', '1999-01');
        $end = \DateTimeImmutable::createFromFormat('Y-m', '2001-12');
        $period = new \DatePeriod($start, new \DateInterval('P1Y'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'total' => 1000],
                ['year' => 2001, 'total' => 3000],
            ])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y', '1999'), 'total' => 1000],
            ['period' => \DateTimeImmutable::createFromFormat('Y', '2000'), 'total' => 0],
            ['period' => \DateTimeImmutable::createFromFormat('Y', '2001'), 'total' => 3000],
        ]);
    }
}

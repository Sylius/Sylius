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

final class DayBasedOrdersTotalProviderSpec extends AbstractOrdersTotalsProviderSpec
{
    protected const DATE_FORMAT = 'Y-m-d';

    private const GROUP_SELECT = [
        'year' => 'YEAR(o.checkoutCompletedAt) AS year',
        'month' => 'MONTH(o.checkoutCompletedAt) AS month',
        'day' => 'DAY(o.checkoutCompletedAt) AS day',
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
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-01 1');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-01 23');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-01'), 'total' => 0],
        ]);
    }

    function it_returns_an_array_of_totals_per_period(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-01 1');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-03 23');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'total' => 1000],
                ['year' => 1999, 'month' => 1, 'day' => 2, 'total' => 2000],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'total' => 3000],
            ])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-01'), 'total' => 1000],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-02'), 'total' => 2000],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-03'), 'total' => 3000],
        ]);
    }

    function it_returns_fills_zeros_in_periods_with_no_totals(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-01 1');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H', '1999-01-03 23');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $orderRepository
            ->getGroupedTotalPaidSalesForChannelInPeriod($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'total' => 1000],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'total' => 3000],
            ])
        ;

        $this->provideForPeriodInChannel($period, $channel)->shouldBeLikeStatisticsCollection([
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-01'), 'total' => 1000],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-02'), 'total' => 0],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d', '1999-01-03'), 'total' => 3000],
        ]);
    }
}

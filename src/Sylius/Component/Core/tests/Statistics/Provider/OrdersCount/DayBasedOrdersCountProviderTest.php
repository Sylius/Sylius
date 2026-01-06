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

namespace Tests\Sylius\Component\Core\Statistics\Provider\OrdersCount;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\DayBasedOrdersCountProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\OrdersCountProviderInterface;

final class DayBasedOrdersCountProviderTest extends TestCase
{
    private const GROUP_SELECT = [
        'year' => 'YEAR(o.checkoutCompletedAt) AS year',
        'month' => 'MONTH(o.checkoutCompletedAt) AS month',
        'day' => 'DAY(o.checkoutCompletedAt) AS day',
    ];

    private MockObject&OrderRepositoryInterface $orderRepository;

    private OrdersCountProviderInterface $provider;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->provider = new DayBasedOrdersCountProvider($this->orderRepository);
    }

    public function testReturnsZerosWhenNoOrdersHaveBeenFoundForPeriod(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 23:59');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $this->orderRepository
            ->expects($this->once())
            ->method('countGroupedPaidForChannelInPeriod')
            ->with($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([]);

        $expected = [
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00'), 'paidOrdersCount' => 0],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-02 00:00'), 'paidOrdersCount' => 0],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 00:00'), 'paidOrdersCount' => 0],
        ];

        $this->assertEquals($expected, $this->provider->provideForPeriodInChannel($period, $channel));
    }

    public function testReturnsAnArrayOrdersCountPerPeriod(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 23:59');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $this->orderRepository
            ->expects($this->once())
            ->method('countGroupedPaidForChannelInPeriod')
            ->with($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'paid_orders_count' => 1],
                ['year' => 1999, 'month' => 1, 'day' => 2, 'paid_orders_count' => 2],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'paid_orders_count' => 3],
            ]);

        $expected = [
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00'), 'paidOrdersCount' => 1],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-02 00:00'), 'paidOrdersCount' => 2],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 00:00'), 'paidOrdersCount' => 3],
        ];

        $this->assertEquals($expected, $this->provider->provideForPeriodInChannel($period, $channel));
    }

    public function testFillsZerosInPeriodsWithNoOrders(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00');
        $end = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 23:59');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $this->orderRepository
            ->expects($this->once())
            ->method('countGroupedPaidForChannelInPeriod')
            ->with($channel, $start, $end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'paid_orders_count' => 1],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'paid_orders_count' => 3],
            ]);

        $expected = [
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00'), 'paidOrdersCount' => 1],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-02 00:00'), 'paidOrdersCount' => 0],
            ['period' => \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 00:00'), 'paidOrdersCount' => 3],
        ];

        $this->assertEquals($expected, $this->provider->provideForPeriodInChannel($period, $channel));
    }
}

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

namespace Tests\Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\DayBasedOrdersTotalProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;

final class DayBasedOrdersTotalProviderTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private ChannelInterface&MockObject $channel;

    private \DateTimeImmutable $start;

    private \DateTimeImmutable $end;

    private \DatePeriod $period;

    private DayBasedOrdersTotalProvider $provider;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->provider = new DayBasedOrdersTotalProvider($this->orderRepository);
        $this->start = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-01 00:00');
        $this->end = \DateTimeImmutable::createFromFormat('Y-m-d H:i', '1999-01-03 23:59');
        $this->period = new \DatePeriod($this->start, new \DateInterval('P1D'), $this->end);
    }

    private const GROUP_SELECT = [
        'year' => 'YEAR(o.checkoutCompletedAt) AS year',
        'month' => 'MONTH(o.checkoutCompletedAt) AS month',
        'day' => 'DAY(o.checkoutCompletedAt) AS day',
    ];

    public function testShouldImplementOrdersTotalsProviderInterface(): void
    {
        $this->assertInstanceOf(OrdersTotalsProviderInterface::class, $this->provider);
    }

    public function testShouldReturnZerosWhenNoTotalsHaveBeenFoundForPeriod(): void
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('getGroupedTotalPaidSalesForChannelInPeriod')
            ->with($this->channel, $this->start, $this->end, self::GROUP_SELECT)
            ->willReturn([]);

        $this->assertEquals(
            [
                ['period' => '1999-01-01', 'total' => 0],
                ['period' => '1999-01-02', 'total' => 0],
                ['period' => '1999-01-03', 'total' => 0],
            ],
            array_map(
                static fn ($item) => ['period' => $item['period']->format('Y-m-d'), 'total' => $item['total']],
                $this->provider->provideForPeriodInChannel($this->period, $this->channel),
            ),
        );
    }

    public function testShouldReturnArrayOfTotalsPerPeriod(): void
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('getGroupedTotalPaidSalesForChannelInPeriod')
            ->with($this->channel, $this->start, $this->end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'total' => 1000],
                ['year' => 1999, 'month' => 1, 'day' => 2, 'total' => 2000],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'total' => 3000],
            ]);

        $this->assertEquals(
            [
                ['period' => '1999-01-01', 'total' => 1000],
                ['period' => '1999-01-02', 'total' => 2000],
                ['period' => '1999-01-03', 'total' => 3000],
            ],
            array_map(
                static fn ($item) => ['period' => $item['period']->format('Y-m-d'), 'total' => $item['total']],
                $this->provider->provideForPeriodInChannel($this->period, $this->channel),
            ),
        );
    }

    public function testShouldFillsZerosInPeriodsWithNoTotals(): void
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('getGroupedTotalPaidSalesForChannelInPeriod')
            ->with($this->channel, $this->start, $this->end, self::GROUP_SELECT)
            ->willReturn([
                ['year' => 1999, 'month' => 1, 'day' => 1, 'total' => 1000],
                ['year' => 1999, 'month' => 1, 'day' => 3, 'total' => 3000],
            ]);

        $this->assertEquals(
            [
                ['period' => '1999-01-01', 'total' => 1000],
                ['period' => '1999-01-02', 'total' => 0],
                ['period' => '1999-01-03', 'total' => 3000],
            ],
            array_map(
                static fn ($item) => ['period' => $item['period']->format('Y-m-d'), 'total' => $item['total']],
                $this->provider->provideForPeriodInChannel($this->period, $this->channel),
            ),
        );
    }
}

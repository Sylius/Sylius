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

namespace Tests\Sylius\Component\Core\Statistics\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProvider;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;

final class BusinessActivitySummaryProviderTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private CustomerRepositoryInterface&MockObject $customerRepository;

    private BusinessActivitySummaryProvider $businessActivitySummaryProvider;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->businessActivitySummaryProvider = new BusinessActivitySummaryProvider(
            $this->orderRepository,
            $this->customerRepository,
        );
    }

    public function testShouldImplementBusinessActivitySummaryProviderInterface(): void
    {
        $this->assertInstanceOf(BusinessActivitySummaryProviderInterface::class, $this->businessActivitySummaryProvider);
    }

    public function testShouldProvideBusinessActivitySummary(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $datePeriod = $this->createMock(\DatePeriod::class);
        $startDate = new \DateTime('01-02-2022');
        $endDate = new \DateTime('01-12-2022');

        $datePeriod->expects($this->exactly(3))->method('getStartDate')->willReturn($startDate);
        $datePeriod->expects($this->exactly(3))->method('getEndDate')->willReturn($endDate);

        $this->orderRepository
            ->expects($this->once())
            ->method('getTotalPaidSalesForChannelInPeriod')
            ->with($channel, $startDate, $endDate)
            ->willReturn(1000);
        $this->orderRepository
            ->expects($this->once())
            ->method('countPaidForChannelInPeriod')
            ->with($channel, $startDate, $endDate)
            ->willReturn(13);
        $this->customerRepository
            ->expects($this->once())
            ->method('countCustomersInPeriod')
            ->with($startDate, $endDate)
            ->willReturn(4);

        $this->assertEquals(
            new BusinessActivitySummary(1000, 13, 4),
            $this->businessActivitySummaryProvider->provide($datePeriod, $channel),
        );
    }
}

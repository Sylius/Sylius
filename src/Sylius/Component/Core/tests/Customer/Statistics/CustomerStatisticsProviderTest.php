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

namespace Tests\Sylius\Component\Core\Customer\Statistics;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProvider;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class CustomerStatisticsProviderTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&RepositoryInterface $channelRepository;

    private ChannelInterface&MockObject $channel;

    private CustomerInterface&MockObject $customer;

    private MockObject&OrderInterface $firstOrder;

    private MockObject&OrderInterface $secondOrder;

    private CustomerStatisticsProvider $provider;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->channelRepository = $this->createMock(RepositoryInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->firstOrder = $this->createMock(OrderInterface::class);
        $this->secondOrder = $this->createMock(OrderInterface::class);
        $this->provider = new CustomerStatisticsProvider($this->orderRepository, $this->channelRepository);
    }

    public function testShouldImplementCustomerStatisticsProviderInterface(): void
    {
        $this->assertInstanceOf(CustomerStatisticsProviderInterface::class, $this->provider);
    }

    public function testShouldReturnEmptyStatisticIfGivenCustomerDoesNotHaveAnyOrders(): void
    {
        $expectedStatistics = new CustomerStatistics([]);

        $this->orderRepository->expects($this->once())->method('findForCustomerStatistics')->with($this->customer)->willReturn([]);

        $this->assertEquals(
            $expectedStatistics,
            $this->provider->getCustomerStatistics($this->customer),
        );
    }

    public function testShouldObtainsCustomerStatisticsFromSingleChannel(): void
    {
        $channelWithoutOrders = $this->createMock(ChannelInterface::class);
        $expectedStatistics = new CustomerStatistics([
            new PerChannelCustomerStatistics(2, 33000, $this->channel),
        ]);

        $this->firstOrder->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->secondOrder->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->firstOrder->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->secondOrder->expects($this->once())->method('getTotal')->willReturn(23000);
        $this->channelRepository->expects($this->once())->method('findAll')->willReturn([$this->channel, $channelWithoutOrders]);
        $this->orderRepository->expects($this->once())->method('findForCustomerStatistics')->with($this->customer)->willReturn([
            $this->firstOrder,
            $this->secondOrder,
        ]);

        $this->assertEquals(
            $expectedStatistics,
            $this->provider->getCustomerStatistics($this->customer),
        );
    }

    public function testShouldObtainCustomerStatisticsFromMultipleChannels(): void
    {
        $secondChannel = $this->createMock(ChannelInterface::class);
        $thirdOrder = $this->createMock(OrderInterface::class);
        $fourthOrder = $this->createMock(OrderInterface::class);
        $fifthOrder = $this->createMock(OrderInterface::class);
        $expectedStatistics = new CustomerStatistics([
            new PerChannelCustomerStatistics(2, 33000, $this->channel),
            new PerChannelCustomerStatistics(3, 11000, $secondChannel),
        ]);

        $this->firstOrder->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->secondOrder->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->firstOrder->expects($this->once())->method('getTotal')->willReturn(10000);
        $this->secondOrder->expects($this->once())->method('getTotal')->willReturn(23000);
        $thirdOrder->expects($this->exactly(2))->method('getChannel')->willReturn($secondChannel);
        $fourthOrder->expects($this->exactly(2))->method('getChannel')->willReturn($secondChannel);
        $fifthOrder->expects($this->exactly(2))->method('getChannel')->willReturn($secondChannel);
        $thirdOrder->expects($this->once())->method('getTotal')->willReturn(2000);
        $fourthOrder->expects($this->once())->method('getTotal')->willReturn(8000);
        $fifthOrder->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->channelRepository->expects($this->once())->method('findAll')->willReturn([$this->channel, $secondChannel]);
        $this->orderRepository->expects($this->once())->method('findForCustomerStatistics')->with($this->customer)->willReturn(
            [$this->firstOrder, $this->secondOrder, $thirdOrder, $fourthOrder, $fifthOrder],
        );

        $this->assertEquals(
            $expectedStatistics,
            $this->provider->getCustomerStatistics($this->customer),
        );
    }
}

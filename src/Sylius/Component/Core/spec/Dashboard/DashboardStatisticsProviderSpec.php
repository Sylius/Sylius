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

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\DashboardStatistics;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class DashboardStatisticsProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerRepositoryInterface $customerRepository): void
    {
        $this->beConstructedWith($orderRepository, $customerRepository);
    }

    function it_implements_a_dashboard_statistics_provider_interface(): void
    {
        $this->shouldImplement(DashboardStatisticsProviderInterface::class);
    }

    function it_obtains_order_and_customer_statistics_by_given_channel(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelInterface $channel,
    ): void {
        $expectedStats = new DashboardStatistics(450, 2, 6, $channel->getWrappedObject());

        $orderRepository->getTotalPaidSalesForChannel($channel)->willReturn(450);
        $orderRepository->countPaidByChannel($channel)->willReturn(2);
        $customerRepository->countCustomers()->willReturn(6);

        $this->getStatisticsForChannel($channel)->shouldBeLike($expectedStats);
    }

    function it_obtains_order_and_customer_statistics_by_given_channel_and_period(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelInterface $channel,
    ): void {
        $expectedStats = new DashboardStatistics(450, 2, 6);

        $yesterday = new \DateTime('yesterday');
        $today = new \DateTime();

        $orderRepository->getTotalPaidSalesForChannelInPeriod($channel, $yesterday, $today)->willReturn(450);
        $orderRepository->countPaidForChannelInPeriod($channel, $yesterday, $today)->willReturn(2);
        $customerRepository->countCustomersInPeriod($yesterday, $today)->willReturn(6);

        $this->getStatisticsForChannelInPeriod($channel, $yesterday, $today)->shouldBeLike($expectedStats);
    }
}

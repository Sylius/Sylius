<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\DashboardStatistics;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProvider;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DashboardStatisticsProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerRepositoryInterface $customerRepository)
    {
        $this->beConstructedWith($orderRepository, $customerRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DashboardStatisticsProvider::class);
    }

    function it_implements_a_dashboard_statistics_provider_interface()
    {
        $this->shouldImplement(DashboardStatisticsProviderInterface::class);
    }

    function it_obtains_order_and_customer_statistics_by_given_channel(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ChannelInterface $channel
    ) {
        $expectedStats = new DashboardStatistics(450, 2, 6);

        $orderRepository->getTotalSalesForChannel($channel)->willReturn(450);
        $orderRepository->countByChannel($channel)->willReturn(2);
        $customerRepository->count()->willReturn(6);

        $this->getStatisticsForChannel($channel)->shouldBeLike($expectedStats);
    }
}

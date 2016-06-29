<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Core\Dashboard\DashboardStatistics;
use Sylius\Core\Dashboard\DashboardStatisticsProvider;
use Sylius\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Core\Repository\OrderRepositoryInterface;
use Sylius\User\Repository\CustomerRepositoryInterface;

/**
 * @mixin DashboardStatisticsProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardStatisticsProviderSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, CustomerRepositoryInterface $customerRepository)
    {
        $this->beConstructedWith($orderRepository, $customerRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Dashboard\DashboardStatisticsProvider');
    }
    
    function it_implements_dashboard_statistics_provider_interface()
    {
        $this->shouldImplement(DashboardStatisticsProviderInterface::class);
    }

    function it_obtains_order_and_customer_statistics_from_the_repositories(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $expectedStats = new DashboardStatistics(450, 2, 6);
       
        $orderRepository->getTotalSales()->willReturn(450);
        $orderRepository->count()->willReturn(2);
        $customerRepository->count()->willReturn(6);
        
        $this->getStatistics()->shouldBeLike($expectedStats);
    }
}

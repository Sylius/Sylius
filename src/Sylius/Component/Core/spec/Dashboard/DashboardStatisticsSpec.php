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

use Sylius\Component\Core\Dashboard\DashboardStatistics;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin DashboardStatistics
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DashboardStatisticsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2564, 24, 10);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Dashboard\DashboardStatistics');
    }

    function it_throws_exception_if_any_of_values_if_not_an_int()
    {
        $this->beConstructedWith('string', 2.5, 'foo');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_has_total_sales_stat()
    {
        $this->getTotalSales()->shouldReturn(2564);
    }

    function it_has_new_orders_stat()
    {
        $this->getNumberOfNewOrders()->shouldReturn(24);
    }

    function ith_has_new_customers_stat()
    {
        $this->getNumberOfNewCustomers()->shouldReturn(10);
    }

    function it_calculates_average_order_value()
    {
        $this->getAverageOrderValue()->shouldReturn(107);
    }

    function it_returns_0_as_average_order_value_when_there_are_no_orders()
    {
        $this->beConstructedWith(0, 0, 2);

        $this->getAverageOrderValue()->shouldReturn(0);
    }
}

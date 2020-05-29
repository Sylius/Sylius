<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;

final class DashboardStatisticsSpec extends ObjectBehavior
{
    function let(ChannelInterface $channel): void
    {
        $this->beConstructedWith(2564, 24, 10, $channel);
    }

    function it_has_total_sales_stat(): void
    {
        $this->getTotalSales()->shouldReturn(2564);
    }

    function it_has_new_orders_stat(): void
    {
        $this->getNumberOfNewOrders()->shouldReturn(24);
    }

    function ith_has_new_customers_stat(): void
    {
        $this->getNumberOfNewCustomers()->shouldReturn(10);
    }

    function it_calculates_an_average_order_value(): void
    {
        $this->getAverageOrderValue()->shouldReturn(107);
    }

    function it_returns_0_as_average_order_value_when_there_are_no_orders(): void
    {
        $this->beConstructedWith(0, 0, 2);

        $this->getAverageOrderValue()->shouldReturn(0);
    }

    function it_returns_channel(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldReturn($channel);
    }
}

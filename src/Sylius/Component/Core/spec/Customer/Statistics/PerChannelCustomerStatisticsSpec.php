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

namespace spec\Sylius\Component\Core\Customer\Statistics;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class PerChannelCustomerStatisticsSpec extends ObjectBehavior
{
    function let(ChannelInterface $channel)
    {
        $this->beConstructedWith(10, 20000, $channel);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PerChannelCustomerStatistics::class);
    }

    function it_throws_an_exception_if_any_of_values_besides_channel_is_not_an_int(ChannelInterface $channel)
    {
        $this->beConstructedWith(new \Datetime(), [], $channel);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_has_number_of_orders()
    {
        $this->getOrdersCount()->shouldReturn(10);
    }

    function it_has_the_combined_value_of_all_orders()
    {
        $this->getOrdersValue()->shouldReturn(20000);
    }

    function it_has_a_clone_of_the_origin_channel_of_orders(ChannelInterface $channel)
    {
        $this->getChannel()->shouldBeLike($channel);
    }

    function it_has_an_average_value_of_an_order()
    {
        $this->getAverageOrderValue()->shouldReturn(2000);
    }
}

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

namespace spec\Sylius\Component\Core\Customer\Statistics;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;

final class PerChannelCustomerStatisticsSpec extends ObjectBehavior
{
    function let(ChannelInterface $channel): void
    {
        $this->beConstructedWith(10, 20000, $channel);
    }

    function it_has_number_of_orders(): void
    {
        $this->getOrdersCount()->shouldReturn(10);
    }

    function it_has_the_combined_value_of_all_orders(): void
    {
        $this->getOrdersValue()->shouldReturn(20000);
    }

    function it_has_a_clone_of_the_origin_channel_of_orders(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldBeLike($channel);
    }

    function it_has_an_average_value_of_an_order(): void
    {
        $this->getAverageOrderValue()->shouldReturn(2000);
    }
}

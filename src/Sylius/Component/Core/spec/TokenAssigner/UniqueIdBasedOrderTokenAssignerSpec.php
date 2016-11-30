<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\TokenAssigner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Core\TokenAssigner\UniqueIdBasedOrderTokenAssigner;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class UniqueIdBasedOrderTokenAssignerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueIdBasedOrderTokenAssigner::class);
    }

    function it_is_an_order_token_assigner()
    {
        $this->shouldImplement(OrderTokenAssignerInterface::class);
    }

    function it_assigns_a_token_value_for_order(OrderInterface $order)
    {
        $order->setTokenValue(Argument::type('string'))->shouldBeCalled();

        $this->assignTokenValue($order);
    }
}

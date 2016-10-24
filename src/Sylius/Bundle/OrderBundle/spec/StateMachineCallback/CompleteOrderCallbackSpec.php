<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\StateMachineCallback;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\StateMachineCallback\CompleteOrderCallback;
use Sylius\Component\Order\Model\OrderInterface;

final class CompleteOrderCallbackSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompleteOrderCallback::class);
    }

    function it_completes_an_order(OrderInterface $order)
    {
        $order->complete()->shouldBeCalled();

        $this->completeOrder($order);
    }
}

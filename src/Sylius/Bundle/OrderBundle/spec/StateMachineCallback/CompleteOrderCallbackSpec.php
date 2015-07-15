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
use Sylius\Component\Order\Model\OrderInterface;

class CompleteOrderCallbackSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\StateMachineCallback\CompleteOrderCallback');
    }

    public function it_completes_order(OrderInterface $order)
    {
        $order->complete()->shouldBeCalled();

        $this->completeOrder($order);
    }
}

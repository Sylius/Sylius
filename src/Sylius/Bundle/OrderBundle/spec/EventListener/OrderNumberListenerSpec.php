<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SequenceBundle\Doctrine\ORM\NumberListener;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberListenerSpec extends ObjectBehavior
{
    function let(NumberListener $listener)
    {
        $this->beConstructedWith($listener);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderNumberListener');
    }

    function it_generates_order_number(NumberListener $listener, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->getNumber()->willReturn(null);

        $listener->enableEntity($order)->shouldBeCalled();

        $this->generateOrderNumber($event);
    }
}

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
use Sylius\Component\Sequence\Number\GeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberListenerSpec extends ObjectBehavior
{
    public function let(GeneratorInterface $generator)
    {
        $this->beConstructedWith($generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderNumberListener');
    }

    function it_generates_order_number($generator, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->getNumber()->willReturn(null);

        $generator->generate($order)->shouldBeCalled();

        $this->generateOrderNumber($event);
    }
}

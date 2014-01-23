<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\StateResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $stateResolver)
    {
        $this->beConstructedWith($stateResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderStateListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringResolveOrderStates($event)
        ;
    }

    function it_resolves_order_states(
            StateResolverInterface $stateResolver,
            GenericEvent $event,
            OrderInterface $order
    )
    {
        $event->getSubject()->willReturn($order);
        $stateResolver->resolveShippingState($order)->shouldBeCalled();
        $stateResolver->resolvePaymentState($order)->shouldBeCalled();

        $this->resolveOrderStates($event);
    }
}

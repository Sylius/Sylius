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
use SM\Event\TransitionEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderStateListenerSpec extends ObjectBehavior
{
    public function let(StateResolverInterface $stateResolver)
    {
        $this->beConstructedWith($stateResolver);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderStateListener');
    }

    public function it_resolves_order_states(
        StateResolverInterface $stateResolver,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $stateResolver->resolveShippingState($order)->shouldBeCalled();
        $stateResolver->resolvePaymentState($order)->shouldBeCalled();

        $this->resolveOrderStates($event);
    }

    public function it_resolves_order_states_with_state_machine_event(
        StateResolverInterface $stateResolver,
        TransitionEvent $event,
        OrderInterface $order,
        StateMachineInterface $sm
    ) {
        $event->getStateMachine()->willReturn($sm);
        $sm->getObject()->willReturn($order);

        $stateResolver->resolveShippingState($order)->shouldBeCalled();
        $stateResolver->resolvePaymentState($order)->shouldBeCalled();

        $this->resolveOrderStatesOnTransition($event);
    }
}

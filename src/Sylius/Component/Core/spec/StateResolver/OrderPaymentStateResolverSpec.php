<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\StateResolver;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\StateResolver\OrderPaymentStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Order\StateResolver\TargetTransitionResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderPaymentStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory, TargetTransitionResolverInterface $transitionResolver)
    {
        $this->beConstructedWith($stateMachineFactory, $transitionResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentStateResolver::class);
    }

    function it_implements_an_order_state_resolver_interface()
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_changes_the_state_of_order_according_to_target_transition_resolver(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        TargetTransitionResolverInterface $transitionResolver,
        OrderInterface $order
    ) {
        $transitionResolver->resolve($order)->willReturn(OrderPaymentTransitions::TRANSITION_REFUND);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $this->resolve($order);
    }
}

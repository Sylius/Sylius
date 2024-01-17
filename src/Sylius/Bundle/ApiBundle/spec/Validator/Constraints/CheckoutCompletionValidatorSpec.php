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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\Transition;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CheckoutCompletion;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CheckoutCompletionValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachineFactory);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_order_token_value_aware_interface(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', $constraint])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_checkout_completion(
        Constraint $constraint,
        OrderTokenValueAwareInterface $orderTokenValueAware,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$orderTokenValueAware, $constraint])
        ;
    }

    function it_throws_an_exception_if_order_with_given_token_value_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        Constraint $constraint,
        OrderTokenValueAwareInterface $orderTokenValueAware,
    ): void {
        $orderTokenValueAware->getOrderTokenValue()->willReturn('xxx');
        $orderRepository->findOneBy(['tokenValue' => 'xxx'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$orderTokenValueAware, $constraint])
        ;
    }

    function it_does_nothing_if_order_can_be_completed(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderTokenValueAwareInterface $orderTokenValueAware,
        WinzouStateMachineInterface $stateMachine,
    ): void {
        $this->initialize($executionContext);

        $orderTokenValueAware->getOrderTokenValue()->willReturn('xxx');
        $orderRepository->findOneBy(['tokenValue' => 'xxx'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(true);
        $stateMachine->getState()->shouldNotBeCalled();
        $stateMachine->getPossibleTransitions()->shouldNotBeCalled();

        $executionContext
            ->addViolation(Argument::cetera())
            ->shouldNotBeCalled()
        ;

        $this->validate($orderTokenValueAware, new CheckoutCompletion());
    }

    function it_adds_violation_if_order_cannot_be_completed(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderTokenValueAwareInterface $orderTokenValueAware,
        WinzouStateMachineInterface $stateMachine,
    ): void {
        $this->initialize($executionContext);

        $orderTokenValueAware->getOrderTokenValue()->willReturn('xxx');
        $orderRepository->findOneBy(['tokenValue' => 'xxx'])->willReturn($order);

        $stateMachineFactory->get($order, 'sylius_order_checkout')->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);
        $stateMachine->getPossibleTransitions()->willReturn(['some_possible_transition', 'another_possible_transition']);

        $order->getState()->willReturn('some_state_that_does_not_allow_to_complete_order');

        $executionContext
            ->addViolation(
                'sylius.order.invalid_state_transition',
                [
                    '%currentState%' => 'some_state_that_does_not_allow_to_complete_order',
                    '%possibleTransitions%' => 'some_possible_transition, another_possible_transition',
                ],
            )
            ->shouldBeCalled()
        ;

        $this->validate($orderTokenValueAware, new CheckoutCompletion());
    }

    function it_uses_the_new_state_machine_abstraction_if_passed(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderTokenValueAwareInterface $orderTokenValueAware,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine);

        $this->initialize($executionContext);

        $orderTokenValueAware->getOrderTokenValue()->willReturn('xxx');
        $orderRepository->findOneBy(['tokenValue' => 'xxx'])->willReturn($order);

        $stateMachine->can($order, 'sylius_order_checkout', OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);
        $stateMachine
            ->getEnabledTransitions($order, 'sylius_order_checkout')
            ->willReturn([new Transition('some_possible_transition', null, null), new Transition('another_possible_transition', null, null)])
        ;

        $order->getState()->willReturn('some_state_that_does_not_allow_to_complete_order');

        $executionContext
            ->addViolation(
                'sylius.order.invalid_state_transition',
                [
                    '%currentState%' => 'some_state_that_does_not_allow_to_complete_order',
                    '%possibleTransitions%' => 'some_possible_transition, another_possible_transition',
                ],
            )
            ->shouldBeCalled()
        ;

        $this->validate($orderTokenValueAware, new CheckoutCompletion());
    }
}

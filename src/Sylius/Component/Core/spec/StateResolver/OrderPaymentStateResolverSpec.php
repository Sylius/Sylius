<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\StateResolver\OrderPaymentStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderPaymentStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentStateResolver::class);
    }

    function it_implements_an_order_state_resolver_interface()
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_refunded_if_all_its_payments_are_refunded(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_refunded_if_its_payments_are_refunded_or_failed_but_at_least_one_is_refunded(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(10000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $secondPayment->getAmount()->willReturn(10000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_paid_if_fully_paid(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $payment
    ) {
        $payment->getAmount()->willReturn(10000);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$payment->getWrappedObject()]);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_paid_if_fully_paid_even_if_previous_payment_was_failed(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(10000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $secondPayment->getAmount()->willReturn(10000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_partially_refunded_if_one_of_the_payment_is_completed(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_completed_if_fully_paid_multiple_payments(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_partially_paid_if_one_of_the_payment_is_processing(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY)->shouldBeCalled();

        $this->resolve($order);
    }
}

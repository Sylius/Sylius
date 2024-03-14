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

namespace spec\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderPaymentStateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_implements_an_order_state_resolver_interface(): void
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_an_order_as_refunded_if_all_its_payments_are_refunded(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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

    function it_uses_the_new_state_machine_if_passed(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $this->beConstructedWith($stateMachine);

        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachine->can($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->willReturn(true);
        $stateMachine->apply($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_refunded_if_its_payments_are_refunded_or_failed_but_at_least_one_is_refunded(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
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

    function it_marks_an_order_as_paid_if_it_does_not_have_any_payments(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
    ) {
        $order->getPayments()->willReturn(new ArrayCollection([]));
        $order->getTotal()->willReturn(0);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_paid_if_fully_paid_even_if_previous_payment_was_failed(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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

    function it_marks_an_order_as_partially_refunded_if_one_of_the_payment_is_refunded(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
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

    function it_marks_an_order_as_authorized_if_all_its_payments_are_authorized(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_AUTHORIZE)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_AUTHORIZE)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_partially_authorized_if_one_of_the_payments_is_processing_and_one_of_the_payments_is_authorized(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $secondPayment->getAmount()->willReturn(4000);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject(), $secondPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE)->shouldBeCalled();

        $this->resolve($order);
    }

    function it_marks_an_order_as_awaiting_payment_if_payments_is_processing(
        FactoryInterface $stateMachineFactory,
        WinzouStateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $firstPayment,
    ): void {
        $firstPayment->getAmount()->willReturn(6000);
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);

        $order
            ->getPayments()
            ->willReturn(new ArrayCollection([$firstPayment->getWrappedObject()]))
        ;
        $order->getTotal()->willReturn(6000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT)->shouldBeCalled();

        $this->resolve($order);
    }
}

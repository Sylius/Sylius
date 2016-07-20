<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class StateResolverSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\StateResolver');
    }

    function it_implements_Sylius_order_state_resolver_interface()
    {
        $this->shouldImplement(StateResolverInterface::class);
    }

    function it_marks_order_as_shipped_if_all_shipments_delivered(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    ) {
        $order->getShipments()->willReturn([$shipment1, $shipment2]);

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $order->setShippingState(OrderShippingStates::SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_completed_if_fully_paid(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $payment
    ) {
        $payment->getAmount()->willReturn(10000);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$payment->getWrappedObject()]);

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_completed_if_fully_paid_multiple_payments(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $payment1,
        PaymentInterface $payment2
    ) {
        $payment1->getAmount()->willReturn(6000);
        $payment1->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $payment2->getAmount()->willReturn(4000);
        $payment2->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$payment1->getWrappedObject(), $payment2->getWrappedObject()]);

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY)->shouldBeCalled();

        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_partially_paid_if_one_of_the_payment_is_processing(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $payment1,
        PaymentInterface $payment2
    ) {
        $payment1->getAmount()->willReturn(6000);
        $payment1->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $payment2->getAmount()->willReturn(4000);
        $payment2->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payments = new ArrayCollection([$payment1->getWrappedObject(), $payment2->getWrappedObject()]);

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);
        $order->getTotal()->willReturn(10000);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY)->willReturn(true);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY)->shouldBeCalled();

        $this->resolvePaymentState($order);
    }
}

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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StateResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\StateResolver');
    }

    function it_implements_Sylius_order_state_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\StateResolverInterface');
    }

    function it_marks_order_as_a_backorders_if_it_contains_backordered_units(OrderInterface $order)
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(true);

        $order->setShippingState(OrderShippingStates::BACKORDER)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_shipped_if_all_shipments_devliered(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $order->setShippingState(OrderShippingStates::SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_partially_shipped_if_not_all_shipments_devliered(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_READY);

        $order->setShippingState(OrderShippingStates::PARTIALLY_SHIPPED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_returned_if_all_shipments_were_returned(
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    )
    {
        $order->isBackorder()->shouldBeCalled()->willReturn(false);
        $order->getShipments()->willReturn(array($shipment1, $shipment2));

        $shipment1->getState()->willReturn(ShipmentInterface::STATE_RETURNED);
        $shipment2->getState()->willReturn(ShipmentInterface::STATE_RETURNED);

        $order->setShippingState(OrderShippingStates::RETURNED)->shouldBeCalled();
        $this->resolveShippingState($order);
    }

    function it_marks_order_as_completed_if_fully_paid(
        OrderInterface $order
    )
    {
        $payment1 = new Payment();
        $payment1->setAmount(10000);
        $payment1->setState(PaymentInterface::STATE_COMPLETED);
        $payments = new ArrayCollection(array($payment1));

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);

        $order->getTotal()->willReturn(10000);
        $order->setPaymentState(PaymentInterface::STATE_COMPLETED)->shouldBeCalled();
        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_completed_if_fully_paid_multiple_payments(
        OrderInterface $order
    )
    {
        $payment1 = new Payment();
        $payment1->setAmount(6000);
        $payment1->setState(PaymentInterface::STATE_COMPLETED);
        $payment2 = new Payment();
        $payment2->setAmount(4000);
        $payment2->setState(PaymentInterface::STATE_COMPLETED);
        $payments = new ArrayCollection(array($payment1, $payment2));

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);

        $order->getTotal()->willReturn(10000);
        $order->setPaymentState(PaymentInterface::STATE_COMPLETED)->shouldBeCalled();
        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_processing_if_partially_paid(
        OrderInterface $order
    )
    {
        $payment1 = new Payment();
        $payment1->setAmount(6000);
        $payment1->setState(PaymentInterface::STATE_COMPLETED);
        $payment2 = new Payment();
        $payment2->setAmount(4000);
        $payment2->setState(PaymentInterface::STATE_NEW);
        $payments = new ArrayCollection(array($payment1, $payment2));

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);

        $order->getTotal()->willReturn(10000);
        $order->setPaymentState(PaymentInterface::STATE_PROCESSING)->shouldBeCalled();
        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_processing_if_one_of_the_payment_is_processing(
        OrderInterface $order
    )
    {
        $payment1 = new Payment();
        $payment1->setAmount(6000);
        $payment1->setState(PaymentInterface::STATE_PROCESSING);
        $payment2 = new Payment();
        $payment2->setAmount(4000);
        $payment2->setState(PaymentInterface::STATE_NEW);
        $payments = new ArrayCollection(array($payment1, $payment2));

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);

        $order->getTotal()->willReturn(10000);
        $order->setPaymentState(PaymentInterface::STATE_PROCESSING)->shouldBeCalled();
        $this->resolvePaymentState($order);
    }

    function it_marks_order_as_new_if_no_payment_is_in_process(
        OrderInterface $order
    )
    {
        $payment1 = new Payment();
        $payment1->setAmount(6000);
        $payment1->setState(PaymentInterface::STATE_NEW);
        $payment2 = new Payment();
        $payment2->setAmount(4000);
        $payment2->setState(PaymentInterface::STATE_NEW);
        $payments = new ArrayCollection(array($payment1, $payment2));

        $order->hasPayments()->willReturn(true);
        $order->getPayments()->willReturn($payments);

        $order->getTotal()->willReturn(10000);
        $order->setPaymentState(PaymentInterface::STATE_NEW)->shouldBeCalled();
        $this->resolvePaymentState($order);
    }
}

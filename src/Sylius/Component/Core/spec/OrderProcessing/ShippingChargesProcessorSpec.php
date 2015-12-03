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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin \Sylius\Component\Core\OrderProcessing\ShippingChargesProcessor
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingChargesProcessorSpec extends ObjectBehavior
{
    function let(
        EventDispatcherInterface $eventDispatcher,
        DelegatingCalculatorInterface $calculator
    )
    {
        $this->beConstructedWith($eventDispatcher, $calculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\ShippingChargesProcessor');
    }

    function it_implements_Sylius_shipping_charges_processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface');
    }

    function it_removes_existing_shipping_adjustments(OrderInterface $order)
    {
        $order->getShipments()->willReturn(array());
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldBeCalled();

        $this->applyShippingCharges($order);
    }

    function it_doesnt_apply_any_shipping_charge_if_order_has_no_shipments(OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldBeCalled();
        $order->getShipments()->willReturn(array());
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->applyShippingCharges($order);
    }

    function it_applies_calculated_shipping_charge_for_each_shipment_associated_with_the_order(
        $calculator,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        EventDispatcherInterface $eventDispatcher
    ) {
        $order->getShipments()->willReturn(array($shipment));

        $calculator->calculate($shipment)->willReturn(450);

        $shipment->getMethod()->willReturn($shippingMethod);
        $shippingMethod->getName()->willReturn('FedEx');

        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldBeCalled();

        $eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
            Argument::type(AdjustmentEvent::class)
        )->shouldBeCalled();

        $this->applyShippingCharges($order);
    }
}

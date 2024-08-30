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

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class ShippingChargesProcessorSpec extends ObjectBehavior
{
    function let(FactoryInterface $adjustmentFactory, DelegatingCalculatorInterface $calculator): void
    {
        $this->beConstructedWith($adjustmentFactory, $calculator);
    }

    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_removes_existing_shipping_adjustments(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(true);

        $order->getShipments()->willReturn(new ArrayCollection([]));

        $this->process($order);
    }

    function it_does_not_apply_any_shipping_charge_if_order_has_no_shipments(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(true);

        $order->getShipments()->willReturn(new ArrayCollection([]));
        $order->addAdjustment(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_applies_calculated_shipping_charge_for_each_shipment_associated_with_the_order(
        FactoryInterface $adjustmentFactory,
        DelegatingCalculatorInterface $calculator,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $adjustmentFactory->createNew()->willReturn($adjustment);
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $calculator->calculate($shipment)->willReturn(450);

        $shipment->getMethod()->willReturn($shippingMethod);
        $shippingMethod->getCode()->willReturn('fedex');
        $shippingMethod->getName()->willReturn('FedEx');

        $adjustment->setAmount(450)->shouldBeCalled();
        $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setLabel('FedEx')->shouldBeCalled();
        $adjustment
            ->setDetails([
                'shippingMethodCode' => 'fedex',
                'shippingMethodName' => 'FedEx',
            ])
            ->shouldBeCalled()
        ;

        $shipment->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldBeCalled();
        $shipment->addAdjustment($adjustment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_is_in_a_state_different_than_cart(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(false);

        $order->getShipments()->shouldNotBeCalled();
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->shouldNotBeCalled();

        $this->process($order);
    }
}

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

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderShipmentProcessorSpec extends ObjectBehavior
{
    function let(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory
    ) {
        $this->beConstructedWith($defaultShippingMethodResolver, $shipmentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderShipmentProcessor::class);
    }

    function it_is_an_order_processor()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_creates_a_single_shipment_with_default_shipping_method_and_assigns_all_units_to_it_when_shipping_is_required(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        OrderItemUnitInterface $itemUnit1,
        OrderItemUnitInterface $itemUnit2,
        ShipmentInterface $shipment,
        ShippingMethodInterface $defaultShippingMethod,
        OrderItemInterface $orderItem
    ) {
        $defaultShippingMethodResolver->getDefaultShippingMethod($shipment)->willReturn($defaultShippingMethod);

        $shipmentFactory->createNew()->willReturn($shipment);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(false);
        $order->getItemUnits()->willReturn([$itemUnit1, $itemUnit2]);

        $shipment->setOrder($order)->shouldBeCalled();
        $shipment->setMethod($defaultShippingMethod)->shouldBeCalled();

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnit1)->shouldBeCalled();
        $shipment->addUnit($itemUnit2)->shouldBeCalled();

        $order->addShipment($shipment)->shouldBeCalled();

        $this->process($order);
    }

    function it_removes_shipments_and_returns_null_when_shipping_is_not_required(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $defaultShippingMethod,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $defaultShippingMethodResolver->getDefaultShippingMethod($shipment)->willReturn($defaultShippingMethod);

        $shipmentFactory->createNew()->willReturn($shipment);

        $order->isShippingRequired()->willReturn(false);

        $productVariant->isShippingRequired()->willReturn(false);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $order->removeShipments()->shouldBeCalled();

        $order->isEmpty()->willReturn(false);

        $this->process($order);
    }

    function it_adds_new_item_units_to_existing_shipment(
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $shipments->first()->willReturn($shipment);

        $orderItem->getVariant()->willReturn($productVariant);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn([$itemUnit, $itemUnitWithoutShipment]);
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_removes_units_before_adding_new_ones(
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment
    ) {
        $shipments->first()->willReturn($shipment);

        $order->isShippingRequired()->willReturn(true);

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn([$itemUnit, $itemUnitWithoutShipment]);
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject()]));
        $shipment->removeUnit($itemUnit)->shouldBeCalled();

        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }
}

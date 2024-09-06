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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class OrderShipmentProcessorSpec extends ObjectBehavior
{
    function let(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
    ): void {
        $this->beConstructedWith($defaultShippingMethodResolver, $shipmentFactory, $shippingMethodsResolver);
    }

    function it_is_an_order_processor(): void
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
        OrderItemInterface $orderItem,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $defaultShippingMethodResolver->getDefaultShippingMethod($shipment)->willReturn($defaultShippingMethod);

        $shipmentFactory->createNew()->willReturn($shipment);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(false);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit1->getWrappedObject(), $itemUnit2->getWrappedObject()]));

        $shipment->setOrder($order)->shouldBeCalled();
        $shipment->setMethod($defaultShippingMethod)->shouldBeCalled();

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnit1)->shouldBeCalled();
        $shipment->addUnit($itemUnit2)->shouldBeCalled();

        $order->addShipment($shipment)->shouldBeCalled();

        $this->process($order);
    }

    function it_does_not_add_new_shipment_if_shipping_method_cannot_be_resolved(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        OrderItemUnitInterface $itemUnit1,
        OrderItemUnitInterface $itemUnit2,
        ShipmentInterface $shipment,
        OrderItemInterface $orderItem,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $defaultShippingMethodResolver->getDefaultShippingMethod($shipment)->willThrow(UnresolvedDefaultShippingMethodException::class);

        $shipmentFactory->createNew()->willReturn($shipment);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(false);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit1->getWrappedObject(), $itemUnit2->getWrappedObject()]));

        $shipment->setOrder($order)->shouldBeCalled();
        $shipment->setMethod(Argument::any())->shouldNotBeCalled();

        $shipment->getUnits()->willReturn(
            new ArrayCollection([]),
            new ArrayCollection([$itemUnit1->getWrappedObject(), $itemUnit2->getWrappedObject()]),
        );
        $shipment->addUnit($itemUnit1)->shouldBeCalled();
        $shipment->addUnit($itemUnit2)->shouldBeCalled();
        $shipment->removeUnit($itemUnit1)->shouldBeCalled();
        $shipment->removeUnit($itemUnit2)->shouldBeCalled();

        $order->addShipment($shipment)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_removes_shipments_and_returns_null_when_shipping_is_not_required(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $defaultShippingMethod,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
    ): void {
        $order->canBeProcessed()->willReturn(true);

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
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $shipments->first()->willReturn($shipment);

        $shipment->getMethod()->willReturn($shippingMethod);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $orderItem->getVariant()->willReturn($productVariant);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject(), $itemUnitWithoutShipment->getWrappedObject()]));
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_adds_new_item_units_to_existing_shipment_without_checking_methods_if_shipping_methods_resolver_is_not_used(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
    ): void {
        $this->beConstructedWith($defaultShippingMethodResolver, $shipmentFactory);

        $order->canBeProcessed()->willReturn(true);

        $shipments->first()->willReturn($shipment);

        $orderItem->getVariant()->willReturn($productVariant);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject(), $itemUnitWithoutShipment->getWrappedObject()]));
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_removes_units_before_adding_new_ones(
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $shipments->first()->willReturn($shipment);

        $shipment->getMethod()->willReturn($shippingMethod);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $order->isShippingRequired()->willReturn(true);

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject(), $itemUnitWithoutShipment->getWrappedObject()]));
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject()]));
        $shipment->removeUnit($itemUnit)->shouldBeCalled();

        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_adds_new_item_units_to_existing_shipment_and_replaces_its_method_if_its_ineligible(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $order,
        ShipmentInterface $shipment,
        Collection $shipments,
        OrderItemUnitInterface $itemUnit,
        OrderItemUnitInterface $itemUnitWithoutShipment,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
    ): void {
        $order->canBeProcessed()->willReturn(true);

        $shipments->first()->willReturn($shipment);

        $shipment->getMethod()->willReturn($firstShippingMethod);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$secondShippingMethod]);

        $defaultShippingMethodResolver->getDefaultShippingMethod($shipment)->willReturn($secondShippingMethod);
        $shipment->setMethod($secondShippingMethod)->shouldBeCalled();

        $orderItem->getVariant()->willReturn($productVariant);

        $order->isShippingRequired()->willReturn(true);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $order->isEmpty()->willReturn(false);
        $order->hasShipments()->willReturn(true);
        $order->getItemUnits()->willReturn(new ArrayCollection([$itemUnit->getWrappedObject(), $itemUnitWithoutShipment->getWrappedObject()]));
        $order->getShipments()->willReturn($shipments);

        $itemUnit->getShipment()->willReturn($shipment);

        $shipment->getUnits()->willReturn(new ArrayCollection([]));
        $shipment->addUnit($itemUnitWithoutShipment)->shouldBeCalled();
        $shipment->addUnit($itemUnit)->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_cannot_be_processed(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(false);

        $order->isEmpty()->shouldNotBeCalled();
        $order->getItems()->shouldNotBeCalled();
        $order->isShippingRequired()->shouldNotBeCalled();
        $order->hasShipments()->shouldNotBeCalled();
        $order->getShipments()->shouldNotBeCalled();

        $this->process($order);
    }
}

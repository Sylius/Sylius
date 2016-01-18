<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\OrderProcessing\OrderShipmentTaxesApplicatorInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\OrderUnitsTaxesApplicatorInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Provider\DefaultTaxZoneProviderInterface;
use Sylius\Component\Core\Taxation\OrderTaxesApplicatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        DefaultTaxZoneProviderInterface $defaultTaxZoneProvider,
        OrderShipmentTaxesApplicatorInterface $orderShipmentTaxesApplicator,
        OrderUnitsTaxesApplicatorInterface $orderUnitsTaxesApplicator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->beConstructedWith(
            $defaultTaxZoneProvider,
            $orderShipmentTaxesApplicator,
            $orderUnitsTaxesApplicator,
            $taxRateResolver,
            $zoneMatcher
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Taxation\OrderTaxesApplicator');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement(OrderTaxesApplicatorInterface::class);
    }

    function it_applies_taxes_for_order_items_units_and_shipment(
        $orderShipmentTaxesApplicator,
        $orderUnitsTaxesApplicator,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $itemTaxRate,
        TaxRateInterface $shippingTaxRate,
        ZoneInterface $zone
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator, $iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem, $orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($itemTaxRate);

        $orderUnitsTaxesApplicator->apply($orderItem, $itemTaxRate)->shouldBeCalled();

        $order->getLastShipment()->willReturn($shipment);
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, array('zone' => $zone))->willReturn($shippingTaxRate);

        $orderShipmentTaxesApplicator->apply($order, $shippingTaxRate)->shouldBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_order_item(Collection $items, OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn(array());
        $order->isEmpty()->willReturn(true);

        $order->getShippingAddress()->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_tax_zone(
        $defaultTaxZoneProvider,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn(null);
        $defaultTaxZoneProvider->getZone()->willReturn(null);

        $taxRateResolver->resolve(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_to_item_units_if_tax_rate_cannot_be_resolved(
        $orderShipmentTaxesApplicator,
        $orderUnitsTaxesApplicator,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ZoneInterface $zone
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);
        $orderItem->getProduct()->willReturn($product);

        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn(null);

        $orderUnitsTaxesApplicator->apply(Argument::any())->shouldNotBeCalled();

        $order->getLastShipment()->willReturn(false);

        $orderShipmentTaxesApplicator->apply(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_shipment_taxes_if_there_is_no_shipment(
        $orderShipmentTaxesApplicator,
        $orderUnitsTaxesApplicator,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        TaxRateInterface $itemTaxRate,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);
        $items->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator, $iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem, $orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($itemTaxRate);

        $orderUnitsTaxesApplicator->apply($orderItem, $itemTaxRate)->shouldBeCalled();

        $order->getLastShipment()->willReturn(false);

        $orderShipmentTaxesApplicator->apply(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_for_shipment_if_shipment_tax_rate_cannot_be_resolved(
        $orderShipmentTaxesApplicator,
        $orderUnitsTaxesApplicator,
        $taxRateResolver,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductInterface $product,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        TaxRateInterface $itemTaxRate,
        ZoneInterface $zone
    ) {
        $order->getItems()->willReturn($items);
        $items->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator, $iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem, $orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $orderItem->getProduct()->willReturn($product);
        $taxRateResolver->resolve($product, array('zone' => $zone))->willReturn($itemTaxRate);

        $orderUnitsTaxesApplicator->apply($orderItem, $itemTaxRate)->shouldBeCalled();

        $order->getLastShipment()->willReturn($shipment);
        $shipment->getMethod()->willReturn($shippingMethod);
        $taxRateResolver->resolve($shippingMethod, array('zone' => $zone))->willReturn(null);

        $orderShipmentTaxesApplicator->apply(Argument::any())->shouldNotBeCalled();

        $this->apply($order);
    }
}

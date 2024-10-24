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
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

final class OrderTaxesProcessorSpec extends ObjectBehavior
{
    function let(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
    ): void {
        $this->beConstructedWith($defaultTaxZoneProvider, $zoneMatcher, $strategyRegistry, $taxationAddressResolver);
    }

    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_processes_taxes_using_a_supported_tax_calculation_strategy(
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ShipmentInterface $shipment,
        AddressInterface $address,
        ZoneInterface $zone,
        TaxCalculationStrategyInterface $strategyOne,
        TaxCalculationStrategyInterface $strategyTwo,
    ): void {
        $order->canBeProcessed()->willReturn(true);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $order->isEmpty()->willReturn(false);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $shipment->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $strategyRegistry->all()->willReturn([$strategyOne, $strategyTwo]);
        $taxationAddressResolver->getTaxationAddressFromOrder($order)->willReturn($address);
        $zoneMatcher->match($address, Scope::TAX)->willReturn($zone);

        $strategyOne->supports($order, $zone)->willReturn(false);
        $strategyOne->applyTaxes($order, $zone)->shouldNotBeCalled();

        $strategyTwo->supports($order, $zone)->willReturn(true);
        $strategyTwo->applyTaxes($order, $zone)->shouldBeCalled();

        $this->process($order);
    }

    function it_processes_taxes_for_the_default_tax_zone(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ShipmentInterface $shipment,
        AddressInterface $address,
        ZoneInterface $zone,
        TaxCalculationStrategyInterface $strategyOne,
    ): void {
        $order->canBeProcessed()->willReturn(true);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $order->isEmpty()->willReturn(false);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $shipment->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $strategyRegistry->all()->willReturn([$strategyOne]);
        $taxationAddressResolver->getTaxationAddressFromOrder($order)->willReturn(null);
        $zoneMatcher->match($address, Scope::TAX)->shouldNotBeCalled();
        $defaultTaxZoneProvider->getZone($order)->willReturn($zone);

        $strategyOne->supports($order, $zone)->willReturn(true);
        $strategyOne->applyTaxes($order, $zone)->shouldBeCalled();

        $this->process($order);
    }

    function it_throws_an_exception_if_there_are_no_supported_tax_calculation_strategies(
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddressInterface $address,
        ZoneInterface $zone,
        TaxCalculationStrategyInterface $strategy,
    ): void {
        $order->canBeProcessed()->willReturn(true);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getShipments()->willReturn(new ArrayCollection([]));
        $order->isEmpty()->willReturn(false);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $taxationAddressResolver->getTaxationAddressFromOrder($order)->willReturn($address);
        $zoneMatcher->match($address, Scope::TAX)->willReturn($zone);

        $strategyRegistry->all()->willReturn([$strategy]);

        $strategy->supports($order, $zone)->willReturn(false);
        $strategy->applyTaxes($order, $zone)->shouldNotBeCalled();

        $this->shouldThrow(UnsupportedTaxCalculationStrategyException::class)->during('process', [$order]);
    }

    function it_does_not_process_taxes_if_there_is_no_order_item(OrderInterface $order): void
    {
        $order->canBeProcessed()->willReturn(true);
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn(new ArrayCollection([]));
        $order->getShipments()->willReturn(new ArrayCollection([]));
        $order->isEmpty()->willReturn(true);

        $order->getBillingAddress()->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_not_process_taxes_if_there_is_no_tax_zone(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddressInterface $address,
    ): void {
        $order->canBeProcessed()->willReturn(true);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getShipments()->willReturn(new ArrayCollection([]));
        $order->isEmpty()->willReturn(false);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $taxationAddressResolver->getTaxationAddressFromOrder($order)->willReturn($address);
        $zoneMatcher->match($address, Scope::TAX)->willReturn(null);

        $defaultTaxZoneProvider->getZone($order)->willReturn(null);

        $strategyRegistry->all()->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_nothing_if_the_order_cannot_be_processed(
        ZoneProviderInterface $defaultTaxZoneProvider,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        TaxationAddressResolverInterface $taxationAddressResolver,
        OrderInterface $order,
    ): void {
        $order->canBeProcessed()->willReturn(false);

        $order->getItems()->shouldNotBeCalled();
        $order->getShipments()->shouldNotBeCalled();
        $order->isEmpty()->shouldNotBeCalled();
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldNotBeCalled();

        $taxationAddressResolver->getTaxationAddressFromOrder($order)->shouldNotBeCalled();
        $defaultTaxZoneProvider->getZone($order)->shouldNotBeCalled();
        $strategyRegistry->all()->shouldNotBeCalled();

        $this->process($order);
    }
}

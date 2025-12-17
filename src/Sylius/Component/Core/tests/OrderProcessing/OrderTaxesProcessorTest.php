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

namespace Tests\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

final class OrderTaxesProcessorTest extends TestCase
{
    private MockObject&ZoneProviderInterface $defaultTaxZoneProvider;

    private MockObject&ZoneMatcherInterface $zoneMatcher;

    private MockObject&PrioritizedServiceRegistryInterface $strategyRegistry;

    private MockObject&TaxationAddressResolverInterface $taxationAddressResolver;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ShipmentInterface $shipment;

    private AddressInterface&MockObject $address;

    private MockObject&ZoneInterface $zone;

    private MockObject&TaxCalculationStrategyInterface $strategy;

    private OrderTaxesProcessor $orderTaxesProcessor;

    protected function setUp(): void
    {
        $this->defaultTaxZoneProvider = $this->createMock(ZoneProviderInterface::class);
        $this->zoneMatcher = $this->createMock(ZoneMatcherInterface::class);
        $this->strategyRegistry = $this->createMock(PrioritizedServiceRegistryInterface::class);
        $this->taxationAddressResolver = $this->createMock(TaxationAddressResolverInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->zone = $this->createMock(ZoneInterface::class);
        $this->strategy = $this->createMock(TaxCalculationStrategyInterface::class);
        $this->orderTaxesProcessor = new OrderTaxesProcessor(
            $this->defaultTaxZoneProvider,
            $this->zoneMatcher,
            $this->strategyRegistry,
            $this->taxationAddressResolver,
        );
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderTaxesProcessor);
    }

    public function testShouldProcessTaxesUsingSupportedTaxCalculationStrategy(): void
    {
        $secondStrategy = $this->createMock(TaxCalculationStrategyInterface::class);
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->orderItem->expects($this->once())->method('removeAdjustmentsRecursively')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shipment->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->strategyRegistry->expects($this->once())->method('all')->willReturn([$this->strategy, $secondStrategy]);
        $this->taxationAddressResolver
            ->expects($this->once())
            ->method('getTaxationAddressFromOrder')
            ->with($this->order)
            ->willReturn($this->address);
        $this->zoneMatcher->expects($this->once())->method('match')->with($this->address, Scope::TAX)->willReturn($this->zone);
        $this->strategy->expects($this->once())->method('supports')->with($this->order, $this->zone)->willReturn(false);
        $this->strategy->expects($this->never())->method('applyTaxes')->with($this->order, $this->zone);
        $secondStrategy->expects($this->once())->method('supports')->with($this->order, $this->zone)->willReturn(true);
        $secondStrategy->expects($this->once())->method('applyTaxes')->with($this->order, $this->zone);

        $this->orderTaxesProcessor->process($this->order);
    }

    public function testShouldProcessTaxesForTheDefaultTaxZone(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->orderItem->expects($this->once())->method('removeAdjustmentsRecursively')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shipment->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->strategyRegistry->expects($this->once())->method('all')->willReturn([$this->strategy]);
        $this->taxationAddressResolver
            ->expects($this->once())
            ->method('getTaxationAddressFromOrder')
            ->with($this->order)
            ->willReturn(null);
        $this->zoneMatcher->expects($this->never())->method('match')->with($this->address, Scope::TAX);
        $this->defaultTaxZoneProvider->expects($this->once())->method('getZone')->with($this->order)->willReturn($this->zone);
        $this->strategy->expects($this->once())->method('supports')->with($this->order, $this->zone)->willReturn(true);
        $this->strategy->expects($this->once())->method('applyTaxes')->with($this->order, $this->zone);

        $this->orderTaxesProcessor->process($this->order);
    }

    public function testShouldThrowExceptionIfThereAreNoSupportedTaxCalculationStrategy(): void
    {
        $this->expectException(UnsupportedTaxCalculationStrategyException::class);
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->orderItem->expects($this->once())->method('removeAdjustmentsRecursively')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->strategyRegistry->expects($this->once())->method('all')->willReturn([$this->strategy]);
        $this->taxationAddressResolver
            ->expects($this->once())
            ->method('getTaxationAddressFromOrder')
            ->with($this->order)
            ->willReturn($this->address);
        $this->zoneMatcher->expects($this->once())->method('match')->with($this->address, Scope::TAX)->willReturn($this->zone);
        $this->strategy->expects($this->once())->method('supports')->with($this->order, $this->zone)->willReturn(false);
        $this->strategy->expects($this->never())->method('applyTaxes')->with($this->order, $this->zone);

        $this->orderTaxesProcessor->process($this->order);
    }

    public function testShouldNotProcessTaxesIfThereIsNoOrderItem(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection());
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->once())->method('isEmpty')->willReturn(true);
        $this->order->expects($this->never())->method('getBillingAddress');

        $this->orderTaxesProcessor->process($this->order);
    }

    public function testShouldNotProcessTaxesIfThereIsNoTaxZone(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->orderItem->expects($this->once())->method('removeAdjustmentsRecursively')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxationAddressResolver
            ->expects($this->once())
            ->method('getTaxationAddressFromOrder')
            ->with($this->order)
            ->willReturn($this->address);
        $this->zoneMatcher->expects($this->once())->method('match')->with($this->address, Scope::TAX)->willReturn(null);
        $this->defaultTaxZoneProvider->expects($this->once())->method('getZone')->with($this->order)->willReturn(null);
        $this->strategyRegistry->expects($this->never())->method('all');

        $this->orderTaxesProcessor->process($this->order);
    }

    public function testShouldDoNothingIfTheOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->order->expects($this->never())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->order->expects($this->never())->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->never())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->never())->method('removeAdjustments')->with(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxationAddressResolver->expects($this->never())->method('getTaxationAddressFromOrder')->with($this->order);
        $this->defaultTaxZoneProvider->expects($this->never())->method('getZone')->with($this->order);
        $this->strategyRegistry->expects($this->never())->method('all');

        $this->orderTaxesProcessor->process($this->order);
    }
}

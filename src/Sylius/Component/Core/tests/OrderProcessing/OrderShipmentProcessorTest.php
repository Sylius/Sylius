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
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class OrderShipmentProcessorTest extends TestCase
{
    private DefaultShippingMethodResolverInterface&MockObject $defaultShippingMethodResolver;

    private FactoryInterface&MockObject $shipmentFactory;

    private MockObject&ShippingMethodsResolverInterface $shippingMethodsResolver;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemUnitInterface $firstItemUnit;

    private MockObject&OrderItemUnitInterface $secondItemUnit;

    private MockObject&ShipmentInterface $shipment;

    private MockObject&ShippingMethodInterface $defaultShippingMethod;

    private Collection&MockObject $shipments;

    private OrderShipmentProcessor $orderShipmentProcessor;

    protected function setUp(): void
    {
        $this->defaultShippingMethodResolver = $this->createMock(DefaultShippingMethodResolverInterface::class);
        $this->shipmentFactory = $this->createMock(FactoryInterface::class);
        $this->shippingMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->secondItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->defaultShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shipments = $this->createMock(Collection::class);
        $this->orderShipmentProcessor = new OrderShipmentProcessor(
            $this->defaultShippingMethodResolver,
            $this->shipmentFactory,
            $this->shippingMethodsResolver,
        );
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderShipmentProcessor);
    }

    public function testShouldCreateSingleShipmentWithDefaultShippingMethodAndAssignsAllUnitsToItWhenShippingIsRequired(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $this->defaultShippingMethodResolver
            ->expects($this->once())
            ->method('getDefaultShippingMethod')
            ->with($this->shipment)
            ->willReturn($this->defaultShippingMethod)
        ;
        $this->shipmentFactory->expects($this->once())->method('createNew')->willReturn($this->shipment);

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);
        $this->order->expects($this->once())->method('getItemUnits')->willReturn(new ArrayCollection([
            $this->firstItemUnit,
            $this->secondItemUnit,
        ]));

        $this->firstItemUnit->expects(self::once())->method('getShippable')->willReturn($firstVariant);
        $this->secondItemUnit->expects(self::once())->method('getShippable')->willReturn($secondVariant);
        $firstVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);
        $secondVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);

        $this->shipment->expects($this->once())->method('setOrder')->with($this->order);
        $this->shipment->expects($this->once())->method('setMethod')->with($this->defaultShippingMethod);
        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([]));

        $addUnitInvokedCount = $this->exactly(2);
        $this->shipment->expects($addUnitInvokedCount)->method('addUnit')->willReturnCallback(
            function (OrderItemUnitInterface $unit) use ($addUnitInvokedCount): void {
                if ($addUnitInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame($this->firstItemUnit, $unit);
                }
                if ($addUnitInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame($this->secondItemUnit, $unit);
                }
            },
        );

        $this->order->expects($this->once())->method('addShipment')->with($this->shipment);

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldNotAddNewShipmentIfShippingMethodCannotBeResolved(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $this->defaultShippingMethodResolver
            ->expects($this->once())
            ->method('getDefaultShippingMethod')
            ->with($this->shipment)
            ->willThrowException(new UnresolvedDefaultShippingMethodException())
        ;
        $this->shipmentFactory->expects($this->once())->method('createNew')->willReturn($this->shipment);

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);
        $this->order->expects($this->once())->method('getItemUnits')->willReturn(new ArrayCollection([
            $this->firstItemUnit,
            $this->secondItemUnit,
        ]));

        $this->firstItemUnit->expects(self::once())->method('getShippable')->willReturn($firstVariant);
        $this->secondItemUnit->expects(self::once())->method('getShippable')->willReturn($secondVariant);
        $firstVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);
        $secondVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);

        $this->shipment->expects($this->once())->method('setOrder')->with($this->order);
        $this->shipment->expects($this->never())->method('setMethod')->with($this->anything());
        $this->shipment->expects($this->exactly(2))->method('getUnits')->willReturnOnConsecutiveCalls(
            new ArrayCollection([]),
            new ArrayCollection([$this->firstItemUnit, $this->secondItemUnit]),
        );

        $addUnitInvokedCount = $this->exactly(2);
        $this->shipment->expects($addUnitInvokedCount)->method('addUnit')->willReturnCallback(
            function (OrderItemUnitInterface $unit) use ($addUnitInvokedCount): void {
                if ($addUnitInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame($this->firstItemUnit, $unit);
                }
                if ($addUnitInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame($this->secondItemUnit, $unit);
                }
            },
        );

        $removeUnitInvokedCount = $this->exactly(2);
        $this->shipment->expects($removeUnitInvokedCount)->method('removeUnit')->willReturnCallback(
            function (OrderItemUnitInterface $unit) use ($removeUnitInvokedCount): void {
                if ($removeUnitInvokedCount->numberOfInvocations() === 1) {
                    $this->assertSame($this->firstItemUnit, $unit);
                }
                if ($removeUnitInvokedCount->numberOfInvocations() === 2) {
                    $this->assertSame($this->secondItemUnit, $unit);
                }
            },
        );

        $this->order->expects($this->never())->method('addShipment')->with($this->shipment);

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldRemoveShipmentAndReturnNullWhenShippingIsNotRequired(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(false);
        $this->order->expects($this->once())->method('removeShipments');

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldAddNewItemUnitsToExistingShipment(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $this->shipments->expects($this->once())->method('first')->willReturn($this->shipment);
        $this->shipment->expects($this->once())->method('getMethod')->willReturn($this->defaultShippingMethod);
        $this->shippingMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shipment)
            ->willReturn([$this->defaultShippingMethod])
        ;

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getItemUnits')->willReturn(new ArrayCollection([
            $this->firstItemUnit,
            $this->secondItemUnit,
        ]));
        $this->order->expects($this->once())->method('getShipments')->willReturn($this->shipments);

        $this->firstItemUnit->expects($this->once())->method('getShipment')->willReturn($this->shipment);
        $this->firstItemUnit->expects(self::once())->method('getShippable')->willReturn($firstVariant);
        $this->secondItemUnit->expects(self::once())->method('getShippable')->willReturn($secondVariant);
        $firstVariant->expects(self::never())->method('isShippingRequired');
        $secondVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);

        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([]));
        $this->shipment->expects($this->once())->method('addUnit')->with($this->secondItemUnit);

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldRemoveUnitsBeforeAddingNewOnes(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $this->shipments->expects($this->once())->method('first')->willReturn($this->shipment);
        $this->shipment->expects($this->once())->method('getMethod')->willReturn($this->defaultShippingMethod);
        $this->shippingMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shipment)
            ->willReturn([$this->defaultShippingMethod])
        ;

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getItemUnits')->willReturn(new ArrayCollection([
            $this->firstItemUnit,
            $this->secondItemUnit,
        ]));
        $this->order->expects($this->once())->method('getShipments')->willReturn($this->shipments);

        $this->firstItemUnit->expects($this->once())->method('getShipment')->willReturn($this->shipment);
        $this->firstItemUnit->expects(self::once())->method('getShippable')->willReturn($firstVariant);
        $this->secondItemUnit->expects(self::once())->method('getShippable')->willReturn($secondVariant);
        $firstVariant->expects(self::never())->method('isShippingRequired');
        $secondVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);

        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([$this->firstItemUnit]));
        $this->shipment->expects($this->once())->method('removeUnit')->with($this->firstItemUnit);
        $this->shipment->expects($this->once())->method('addUnit')->with($this->secondItemUnit);

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldAddsNewItemUnitsToExistingShipmentAndReplacesItsMethodIfItsIneligible(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);
        $secondShippingMethod = $this->createMock(ShippingMethodInterface::class);

        $this->shipments->expects($this->once())->method('first')->willReturn($this->shipment);
        $this->shipment->expects($this->once())->method('getMethod')->willReturn($this->defaultShippingMethod);
        $this->shippingMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->shipment)
            ->willReturn([$secondShippingMethod])
        ;
        $this->defaultShippingMethodResolver
            ->expects($this->once())
            ->method('getDefaultShippingMethod')
            ->willReturn($secondShippingMethod)
        ;
        $this->shipment->expects($this->once())->method('setMethod')->with($secondShippingMethod);

        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('isShippingRequired')->willReturn(true);
        $this->order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getItemUnits')->willReturn(new ArrayCollection([
            $this->firstItemUnit,
            $this->secondItemUnit,
        ]));
        $this->order->expects($this->once())->method('getShipments')->willReturn($this->shipments);

        $this->firstItemUnit->expects($this->once())->method('getShipment')->willReturn($this->shipment);
        $this->firstItemUnit->expects(self::once())->method('getShippable')->willReturn($firstVariant);
        $this->secondItemUnit->expects(self::once())->method('getShippable')->willReturn($secondVariant);
        $firstVariant->expects(self::never())->method('isShippingRequired');
        $secondVariant->expects(self::once())->method('isShippingRequired')->willReturn(true);

        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection());
        $this->shipment->expects($this->once())->method('addUnit')->with($this->secondItemUnit);

        $this->orderShipmentProcessor->process($this->order);
    }

    public function testShouldDoNothingIfTheOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->order->expects($this->never())->method('isShippingRequired');
        $this->order->expects($this->never())->method('getItems');
        $this->order->expects($this->never())->method('isEmpty');
        $this->order->expects($this->never())->method('hasShipments');
        $this->order->expects($this->never())->method('getShipments');

        $this->orderShipmentProcessor->process($this->order);
    }
}

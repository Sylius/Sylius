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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessor;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ShippingChargesProcessorTest extends TestCase
{
    private FactoryInterface&MockObject $adjustmentFactory;

    private DelegatingCalculatorInterface&MockObject $calculator;

    private MockObject&OrderInterface $order;

    private ShippingChargesProcessor $shippingChargesProcessor;

    protected function setUp(): void
    {
        $this->adjustmentFactory = $this->createMock(FactoryInterface::class);
        $this->calculator = $this->createMock(DelegatingCalculatorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->shippingChargesProcessor = new ShippingChargesProcessor(
            $this->adjustmentFactory,
            $this->calculator,
        );
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->shippingChargesProcessor);
    }

    public function testShouldRemoveExistingShippingAdjustments(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection());

        $this->shippingChargesProcessor->process($this->order);
    }

    public function testShouldNotApplyAnyShippingChargeIfOrderHasNoShipments(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->shippingChargesProcessor->process($this->order);
    }

    public function it_applies_calculated_shipping_charge_for_each_shipment_associated_with_the_order(
    ): void {
        $shipment = $this->createMock(ShipmentInterface::class);
        $adjustment = $this->createMock(AdjustmentInterface::class);
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$shipment]));
        $this->adjustmentFactory->expects($this->once())->method('createNew')->willReturn($adjustment);
        $this->calculator->expects($this->once())->method('calculate')->with($shipment)->willReturn(450);
        $shipment->expects($this->once())->method('getMethod')->willReturn($shippingMethod);
        $shippingMethod->expects($this->once())->method('getCode')->willReturn('fedex');
        $shippingMethod->expects($this->once())->method('getName')->willReturn('FedEx');
        $adjustment->expects($this->once())->method('setAmount')->with(450);
        $adjustment->expects($this->once())->method('setType')->with(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $adjustment->expects($this->once())->method('setLabel')->with('FedEx');
        $adjustment->expects($this->once())->method('setDetails')->with([
            'shippingMethodCode' => 'fedex',
            'shippingMethodName' => 'FedEx',
        ]);
        $shipment->expects($this->once())->method('removeAdjustments')->with(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shipment->expects($this->once())->method('addAdjustment')->with($adjustment);

        $this->shippingChargesProcessor->process($this->order);
    }

    public function testShouldDoNothingIfOrderIsInDifferentStateThanCart(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->order->expects($this->never())->method('getShipments');
        $this->order->expects($this->never())->method('removeAdjustments')->with(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $this->shippingChargesProcessor->process($this->order);
    }
}

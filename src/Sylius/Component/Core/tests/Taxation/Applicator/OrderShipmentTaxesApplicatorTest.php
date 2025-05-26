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

namespace Tests\Sylius\Component\Core\Taxation\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderShipmentTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class OrderShipmentTaxesApplicatorTest extends TestCase
{
    private CalculatorInterface&MockObject $calculator;

    private AdjustmentFactoryInterface&MockObject $adjustmentsFactory;

    private MockObject&TaxRateResolverInterface $taxRateResolver;

    private OrderShipmentTaxesApplicator $applicator;

    private AdjustmentInterface&MockObject $shippingTaxAdjustment;

    private MockObject&OrderInterface $order;

    private MockObject&ShipmentInterface $firstShipment;

    private MockObject&ShipmentInterface $secondShipment;

    private MockObject&ShippingMethodInterface $firstShippingMethod;

    private MockObject&ShippingMethodInterface $secondShippingMethod;

    private MockObject&TaxRateInterface $taxRate;

    private MockObject&ZoneInterface $zone;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(CalculatorInterface::class);
        $this->adjustmentsFactory = $this->createMock(AdjustmentFactoryInterface::class);
        $this->taxRateResolver = $this->createMock(TaxRateResolverInterface::class);
        $this->shippingTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstShipment = $this->createMock(ShipmentInterface::class);
        $this->secondShipment = $this->createMock(ShipmentInterface::class);
        $this->firstShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->secondShippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->zone = $this->createMock(ZoneInterface::class);
        $this->applicator = new OrderShipmentTaxesApplicator(
            $this->calculator,
            $this->adjustmentsFactory,
            $this->taxRateResolver,
        );
    }

    public function testShouldImplementOrderShipmentTaxesApplicatorInterface(): void
    {
        $this->assertInstanceOf(OrderTaxesApplicatorInterface::class, $this->applicator);
    }

    public function testShouldApplyShipmentTaxesOnOrderBasedOnShipmentAdjustmentsPromotionsAndRate(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->firstShipment]));
        $this->firstShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(1000);
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->firstShippingMethod->expects($this->once())->method('getCode')->willReturn('fedex');
        $this->firstShippingMethod->expects($this->once())->method('getName')->willReturn('FedEx');
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->firstShippingMethod, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->taxRate->expects($this->once())->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->once())->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->once())->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->once())->method('getAmount')->willReturn(0.1);
        $this->taxRate->expects($this->once())->method('isIncludedInPrice')->willReturn(false);
        $this->calculator->expects($this->once())->method('calculate')->with(1000, $this->taxRate)->willReturn(100.0);
        $this->adjustmentsFactory->expects($this->once())->method('createWithData')
            ->with(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                100,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($this->shippingTaxAdjustment);
        $this->firstShipment->expects($this->once())->method('addAdjustment')->with($this->shippingTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldApplyTaxesOnMultipleShipmentsBasedOnShipmentAdjustmentsPromotionsAndRate(): void
    {
        $secondShippingTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->firstShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(600);
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->secondShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(400);
        $this->secondShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->firstShippingMethod->expects($this->exactly(2))->method('getCode')->willReturn('fedex');
        $this->firstShippingMethod->expects($this->exactly(2))->method('getName')->willReturn('FedEx');
        $this->taxRateResolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->with($this->firstShippingMethod, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->taxRate->expects($this->exactly(2))->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->exactly(2))->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->exactly(2))->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->exactly(2))->method('getAmount')->willReturn(0.1);
        $this->taxRate->expects($this->exactly(2))->method('isIncludedInPrice')->willReturn(false);
        $this->calculator->expects($this->exactly(2))->method('calculate')->willReturnCallback(function ($amount) {
            if ($amount === 600) {
                return 60.0;
            }

            return 40.0;
        });
        $this->adjustmentsFactory->expects($this->exactly(2))->method('createWithData')->willReturnMap([
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                60,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
                $this->shippingTaxAdjustment,
            ],
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
                $secondShippingTaxAdjustment,
            ],
        ]);
        $this->firstShipment->expects($this->once())->method('addAdjustment')->with($this->shippingTaxAdjustment);
        $this->secondShipment->expects($this->once())->method('addAdjustment')->with($secondShippingTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldApplyTaxesOnMultipleShipmentsWHenThereIsNoTaxRateForOneOfThem(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->firstShipment->expects($this->never())->method('getAdjustmentsTotal')->willReturn(600);
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->secondShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(400);
        $this->secondShipment->expects($this->once())->method('getMethod')->willReturn($this->secondShippingMethod);
        $this->firstShippingMethod->expects($this->never())->method('getCode');
        $this->firstShippingMethod->expects($this->never())->method('getName');
        $this->secondShippingMethod->expects($this->once())->method('getCode')->willReturn('fedex');
        $this->secondShippingMethod->expects($this->once())->method('getName')->willReturn('FedEx');
        $this->taxRateResolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->willReturnMap([
                [$this->firstShippingMethod, ['zone' => $this->zone], null],
                [$this->secondShippingMethod, ['zone' => $this->zone], $this->taxRate],
            ]);
        $this->taxRate->expects($this->once())->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->once())->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->once())->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->once())->method('getAmount')->willReturn(0.1);
        $this->taxRate->expects($this->once())->method('isIncludedInPrice')->willReturn(false);
        $this->calculator->expects($this->once())->method('calculate')->with(400, $this->taxRate)->willReturn(40.0);
        $this->adjustmentsFactory->expects($this->once())->method('createWithData')
            ->with(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($this->shippingTaxAdjustment);
        $this->firstShipment->expects($this->never())->method('addAdjustment')->with($this->anything());
        $this->secondShipment->expects($this->once())->method('addAdjustment')->with($this->shippingTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldApplyTaxesOnMultipleShipmentsWhenOneOfThemHasZeroTaxAmount(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->firstShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(600);
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->secondShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(400);
        $this->secondShipment->expects($this->once())->method('getMethod')->willReturn($this->secondShippingMethod);
        $this->firstShippingMethod->expects($this->never())->method('getCode')->willReturn('dhl');
        $this->firstShippingMethod->expects($this->never())->method('getName')->willReturn('DHL');
        $this->secondShippingMethod->expects($this->once())->method('getCode')->willReturn('fedex');
        $this->secondShippingMethod->expects($this->once())->method('getName')->willReturn('FedEx');
        $this->taxRateResolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->willReturnMap([
                [$this->firstShippingMethod, ['zone' => $this->zone], $this->taxRate],
                [$this->secondShippingMethod, ['zone' => $this->zone], $this->taxRate],
            ]);
        $this->taxRate->expects($this->once())->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->once())->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->once())->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->once())->method('getAmount')->willReturn(0.1);
        $this->taxRate->expects($this->once())->method('isIncludedInPrice')->willReturn(false);
        $this->calculator->expects($this->exactly(2))->method('calculate')->willReturnCallback(function ($amount) {
            if ($amount === 600.0) {
                return 0.0;
            }

            return 40.0;
        });
        $this->adjustmentsFactory->expects($this->once())->method('createWithData')
            ->with(
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                40,
                false,
                [
                    'shippingMethodCode' => 'fedex',
                    'shippingMethodName' => 'FedEx',
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
            )
            ->willReturn($this->shippingTaxAdjustment);
        $this->firstShipment->expects($this->never())->method('addAdjustment')->with($this->anything());
        $this->secondShipment->expects($this->once())->method('addAdjustment')->with($this->shippingTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldDoNothingIfTaxAmountIsZero(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->firstShipment]));
        $this->firstShipment->expects($this->once())->method('getAdjustmentsTotal')->willReturn(1000);
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->firstShippingMethod, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->calculator->expects($this->once())->method('calculate')->with(1000, $this->taxRate)->willReturn(0.00);
        $this->adjustmentsFactory->expects($this->never())->method('createWithData')->with($this->anything());
        $this->order->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldThrowExceptionIfOrderHasNoShipmentButShipmentTotalIsGreaterThanZero(): void
    {
        $this->expectException(\LogicException::class);
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(10);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(false);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldDoNothingIfTaxRateCannotBeResolved(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('hasShipments')->willReturn(true);
        $this->order->expects($this->once())->method('getShipments')->willReturn(new ArrayCollection([$this->firstShipment]));
        $this->firstShipment->expects($this->once())->method('getMethod')->willReturn($this->firstShippingMethod);
        $this->taxRateResolver->expects($this->once())
            ->method('resolve')
            ->with($this->firstShippingMethod, ['zone' => $this->zone])
            ->willReturn(null);
        $this->calculator->expects($this->never())->method('calculate')->with($this->anything());
        $this->order->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldDoNothingIfOrderHasZeroShippingTotal(): void
    {
        $this->order->expects($this->once())->method('getShippingTotal')->willReturn(0);
        $this->taxRateResolver->expects($this->never())->method('resolve')->with($this->anything());
        $this->order->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }
}

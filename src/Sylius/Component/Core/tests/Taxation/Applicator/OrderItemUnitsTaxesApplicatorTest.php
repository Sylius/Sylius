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
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class OrderItemUnitsTaxesApplicatorTest extends TestCase
{
    private CalculatorInterface&MockObject $calculator;

    private AdjustmentFactoryInterface&MockObject $adjustmentsFactory;

    private MockObject&TaxRateResolverInterface $taxRateResolver;

    private MockObject&ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&OrderItemUnitInterface $firstUnit;

    private MockObject&OrderItemUnitInterface $secondUnit;

    private MockObject&TaxRateInterface $taxRate;

    private MockObject&ZoneInterface $zone;

    private OrderItemUnitsTaxesApplicator $applicator;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(CalculatorInterface::class);
        $this->adjustmentsFactory = $this->createMock(AdjustmentFactoryInterface::class);
        $this->taxRateResolver = $this->createMock(TaxRateResolverInterface::class);
        $this->proportionalIntegerDistributor = $this->createMock(ProportionalIntegerDistributorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->firstUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->secondUnit = $this->createMock(OrderItemUnitInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->zone = $this->createMock(ZoneInterface::class);
        $this->applicator = new OrderItemUnitsTaxesApplicator(
            $this->calculator,
            $this->adjustmentsFactory,
            $this->taxRateResolver,
            $this->proportionalIntegerDistributor,
        );
    }

    public function testShouldImplementOrderShipmentTaxesApplicatorInterface(): void
    {
        $this->assertInstanceOf(OrderTaxesApplicatorInterface::class, $this->applicator);
    }

    public function testShouldDoNothingIfOrderItemHasNoUnits(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection());
        $this->taxRateResolver->expects($this->once())->method('resolve')->willReturn($this->taxRate);
        $this->proportionalIntegerDistributor->expects($this->once())->method('distribute')->with([], 0)->willReturn([]);
        $this->calculator->expects($this->never())->method('calculate')->with($this->anything());
        $this->adjustmentsFactory->expects($this->never())->method('createWithData')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldDoNothingIfTaxRateCannotBeResolved(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->never())->method('getUnits');
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->productVariant, ['zone' => $this->zone])
            ->willReturn(null);
        $this->calculator->expects($this->never())->method('calculate')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldNotApplyTaxesWithAmountZero(): void
    {
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->firstUnit,
            $this->secondUnit,
        ]));
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->productVariant, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->firstUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->secondUnit->expects($this->once())->method('getTotal')->willReturn(900);
        $this->calculator->expects($this->exactly(2))->method('calculate')->withAnyParameters()->willReturn(0.00);
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([0, 0], 0)
            ->willReturn([0, 0]);
        $this->adjustmentsFactory
            ->expects($this->never())
            ->method('createWithData')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT, $this->anything());
        $this->firstUnit->expects($this->never())->method('addAdjustment')->with($this->anything());
        $this->secondUnit->expects($this->never())->method('addAdjustment')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldApplyTaxesOnUnitsBasedOnItemTotalAndRateWithDistributionOnUnits(): void
    {
        $firstTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondTaxAdjustment = $this->createMock(AdjustmentInterface::class);

        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->orderItem->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->firstUnit,
            $this->secondUnit,
        ]));
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->productVariant, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->taxRate->expects($this->exactly(2))->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->exactly(2))->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->exactly(2))->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->exactly(2))->method('getAmount')->willReturn(0.1004);
        $this->taxRate->expects($this->exactly(2))->method('isIncludedInPrice')->willReturn(false);
        $this->firstUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->secondUnit->expects($this->once())->method('getTotal')->willReturn(900);
        $this->calculator->expects($this->exactly(2))->method('calculate')->willReturnCallback(function ($amount) {
            if ($amount === 1000.0) {
                return 100.40;
            }

            return 90.36;
        });
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([100, 90], 191)
            ->willReturn([101, 90]);
        $this->adjustmentsFactory->expects($this->exactly(2))->method('createWithData')->willReturnMap([
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                101,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1004,
                ],
                $firstTaxAdjustment,
            ],
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                90,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1004,
                ],
                $secondTaxAdjustment,
            ],
        ]);
        $this->firstUnit->expects($this->once())->method('addAdjustment')->with($firstTaxAdjustment);
        $this->secondUnit->expects($this->once())->method('addAdjustment')->with($secondTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }
}

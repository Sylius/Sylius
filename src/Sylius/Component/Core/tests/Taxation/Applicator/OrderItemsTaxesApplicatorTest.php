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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class OrderItemsTaxesApplicatorTest extends TestCase
{
    private CalculatorInterface&MockObject $calculator;

    private AdjustmentFactoryInterface&MockObject $adjustmentsFactory;

    private IntegerDistributorInterface&MockObject $distributor;

    private MockObject&TaxRateResolverInterface $taxRateResolver;

    private MockObject&ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&OrderItemUnitInterface $unit;

    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&TaxRateInterface $taxRate;

    private MockObject&ZoneInterface $zone;

    private OrderItemsTaxesApplicator $applicator;

    protected function setUp(): void
    {
        $this->calculator = $this->createMock(CalculatorInterface::class);
        $this->adjustmentsFactory = $this->createMock(AdjustmentFactoryInterface::class);
        $this->distributor = $this->createMock(IntegerDistributorInterface::class);
        $this->taxRateResolver = $this->createMock(TaxRateResolverInterface::class);
        $this->proportionalIntegerDistributor = $this->createMock(ProportionalIntegerDistributorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->unit = $this->createMock(OrderItemUnitInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->zone = $this->createMock(ZoneInterface::class);
        $this->applicator = new OrderItemsTaxesApplicator(
            $this->calculator,
            $this->adjustmentsFactory,
            $this->distributor,
            $this->taxRateResolver,
            $this->proportionalIntegerDistributor,
        );
    }

    public function testShouldImplementOrderTaxesApplicatorInterface(): void
    {
        $this->assertInstanceOf(OrderTaxesApplicatorInterface::class, $this->applicator);
    }

    public function testShouldThrowInvalidArgumentExceptionIfOrderItemHasZeroQuantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(0);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldApplyTaxesOnUnitsBasedOnItemTotalAndRateWithDistributionOnItems(
    ): void {
        $secondOrderItem = $this->createMock(OrderItemInterface::class);
        $secondUnit = $this->createMock(OrderItemUnitInterface::class);
        $thirdUnit = $this->createMock(OrderItemUnitInterface::class);
        $secondProductVariant = $this->createMock(ProductVariantInterface::class);
        $firstTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $thirdTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->order->expects($this->exactly(2))->method('getItems')->willReturn(new ArrayCollection([
            $this->orderItem,
            $secondOrderItem,
        ]));
        $this->orderItem->expects($this->exactly(2))->method('getQuantity')->willReturn(2);
        $this->orderItem->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->orderItem->expects($this->once())->method('getUnits')->willReturn((new ArrayCollection([
            $this->unit,
            $secondUnit,
        ])));
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $secondOrderItem->expects($this->exactly(2))->method('getQuantity')->willReturn(1);
        $secondOrderItem->expects($this->once())->method('getTotal')->willReturn(1000);
        $secondOrderItem->expects($this->once())->method('getUnits')->willReturn((new ArrayCollection([$thirdUnit])));
        $secondOrderItem->expects($this->once())->method('getVariant')->willReturn($secondProductVariant);
        $this->taxRateResolver->expects($this->exactly(2))->method('resolve')->willReturnMap([
            [$this->productVariant, ['zone' => $this->zone], $this->taxRate],
            [$secondProductVariant, ['zone' => $this->zone], $this->taxRate],
        ]);
        $this->calculator->expects($this->exactly(2))->method('calculate')->with(1000, $this->taxRate)->willReturn(100.40);
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([100, 100], 201)
            ->willReturn([101, 100]);
        $this->taxRate->expects($this->exactly(3))->method('getLabel')->willReturn('Simple tax (10%)');
        $this->taxRate->expects($this->exactly(3))->method('getCode')->willReturn('simple_tax');
        $this->taxRate->expects($this->exactly(3))->method('getName')->willReturn('Simple tax');
        $this->taxRate->expects($this->exactly(3))->method('getAmount')->willReturn(0.1);
        $this->taxRate->expects($this->exactly(3))->method('isIncludedInPrice')->willReturn(false);
        $this->distributor->expects($this->exactly(2))->method('distribute')->willReturnMap([
            [101.0, 2, [51, 50]],
            [100.0, 1, [100]],
        ]);
        $this->adjustmentsFactory->expects($this->exactly(3))->method('createWithData')->willReturnMap([
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                51,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
                $firstTaxAdjustment,
            ],
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                50,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
                $secondTaxAdjustment,
            ],
            [
                AdjustmentInterface::TAX_ADJUSTMENT,
                'Simple tax (10%)',
                100,
                false,
                [
                    'taxRateCode' => 'simple_tax',
                    'taxRateName' => 'Simple tax',
                    'taxRateAmount' => 0.1,
                ],
                $thirdTaxAdjustment,
            ],
        ]);
        $this->unit->expects($this->once())->method('addAdjustment')->with($firstTaxAdjustment);
        $secondUnit->expects($this->once())->method('addAdjustment')->with($secondTaxAdjustment);
        $thirdUnit->expects($this->once())->method('addAdjustment')->with($thirdTaxAdjustment);

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldNotApplyTaxesWithAmountZero(): void
    {
        $this->order->expects($this->exactly(2))->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->orderItem->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->productVariant, ['zone' => $this->zone])
            ->willReturn($this->taxRate);
        $this->calculator->expects($this->once())->method('calculate')->with(1000, $this->taxRate)->willReturn(0.00);
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([0], 0)
            ->willReturn([0, 0]);
        $this->distributor->expects($this->never())->method('distribute')->with($this->anything());
        $this->adjustmentsFactory->expects($this->never())->method('createWithData')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldNotApplyTaxesWithDistributionOnItemsIfTheGivenItemHasNoTaxRate(): void
    {
        $this->order->expects($this->exactly(2))->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->orderItem->expects($this->once())->method('getVariant')->willReturn($this->productVariant);
        $this->taxRateResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->productVariant, ['zone' => $this->zone])
            ->willReturn(null);
        $this->calculator->expects($this->never())->method('calculate')->with(1000, $this->isNull());
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([0], 0)
            ->willReturn([0, 0]);
        $this->distributor->expects($this->never())->method('distribute')->with($this->anything());
        $this->adjustmentsFactory->expects($this->never())->method('createWithData')->with($this->anything());

        $this->applicator->apply($this->order, $this->zone);
    }

    public function testShouldThrowInvalidArgumentExceptionIfOrderItemHasZeroQuantityDuringDistributionOnItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->order->expects($this->once())->method('getItems')->willReturn(new ArrayCollection([$this->orderItem]));
        $this->orderItem->expects($this->once())->method('getQuantity')->willReturn(0);

        $this->applicator->apply($this->order, $this->zone);
    }
}

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

namespace Tests\Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class OrderItemTest extends TestCase
{
    private OrderItem $orderItem;

    private MockObject&OrderInterface $order;

    private AdjustmentInterface&MockObject $itemAdjustment;

    private AdjustmentInterface&MockObject $unitAdjustment1;

    private AdjustmentInterface&MockObject $unitAdjustment2;

    private AdjustmentInterface&MockObject $unitAdjustment3;

    private MockObject&OrderItemUnitInterface $unit1;

    private MockObject&OrderItemUnitInterface $unit2;

    private AdjustmentInterface&MockObject $taxAdjustment1;

    private AdjustmentInterface&MockObject $taxAdjustment2;

    protected function setUp(): void
    {
        $this->orderItem = new OrderItem();
        $this->order = $this->createMock(OrderInterface::class);
        $this->itemAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->unitAdjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->unitAdjustment2 = $this->createMock(AdjustmentInterface::class);
        $this->unitAdjustment3 = $this->createMock(AdjustmentInterface::class);
        $this->unit1 = $this->createMock(OrderItemUnitInterface::class);
        $this->unit2 = $this->createMock(OrderItemUnitInterface::class);
        $this->taxAdjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->taxAdjustment2 = $this->createMock(AdjustmentInterface::class);
    }

    public function testImplementsAnOrderItemInterface(): void
    {
        $this->assertInstanceOf(OrderItemInterface::class, $this->orderItem);
    }

    public function testImplementsAnAdjustableInterface(): void
    {
        $this->assertInstanceOf(AdjustableInterface::class, $this->orderItem);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->orderItem->getId());
    }

    public function testDoesNotBelongToAnOrderByDefault(): void
    {
        $this->assertNull($this->orderItem->getOrder());
    }

    public function testAllowsAssigningItselfToAnOrder(): void
    {
        $this->order
            ->expects($this->once())
            ->method('hasItem')
            ->with($this->orderItem)
            ->willReturn(false)
        ;
        $this->order->expects($this->once())->method('addItem')->with($this->orderItem);

        $this->orderItem->setOrder($this->order);

        $this->assertSame($this->order, $this->orderItem->getOrder());
    }

    public function testAllowsDetachingItselfFromAnOrder(): void
    {
        $this->order->expects($this->once())->method('addItem')->with($this->orderItem);
        $this->order
            ->expects($this->once())
            ->method('hasItem')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $this->order->expects($this->once())->method('removeItem')->with($this->orderItem);

        $this->orderItem->setOrder($this->order);
        $this->assertSame($this->order, $this->orderItem->getOrder());

        $this->orderItem->setOrder(null);
        $this->assertNull($this->orderItem->getOrder());
    }

    public function testDoesNotSetOrderIfItIsAlreadySet(): void
    {
        $this->order->expects($this->once())->method('addItem')->with($this->orderItem);

        $this->order
            ->expects($this->once())
            ->method('hasItem')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $this->orderItem->setOrder($this->order);
        $this->assertSame($this->order, $this->orderItem->getOrder());

        $this->orderItem->setOrder($this->order);
        $this->assertSame($this->order, $this->orderItem->getOrder());
    }

    public function testHasQuantityEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->orderItem->getQuantity());
    }

    public function testHasUnitPriceEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->orderItem->getUnitPrice());
    }

    public function testHasOriginalUnitPriceEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->orderItem->getOriginalUnitPrice());
    }

    public function testItsUnitPriceShouldAcceptOnlyInteger(): void
    {
        $this->orderItem->setUnitPrice(4498);
        $this->assertSame(4498, $this->orderItem->getUnitPrice());
    }

    public function testItsOriginalUnitPriceShouldAcceptOnlyInteger(): void
    {
        $this->orderItem->setOriginalUnitPrice(4498);
        $this->assertSame(4498, $this->orderItem->getOriginalUnitPrice());
    }

    public function testHasTotalEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->orderItem->getTotal());
    }

    public function testReturnsAdjustmentsRecursively(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->once())->method('getTotal')->willReturn(100);
        $this->unit1
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(null)
            ->willReturn(new ArrayCollection([$this->unitAdjustment1]))
        ;

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->once())->method('getTotal')->willReturn(100);
        $this->unit2
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(null)
            ->willReturn(new ArrayCollection([$this->unitAdjustment2]))
        ;

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);

        $this->orderItem->addAdjustment($this->itemAdjustment);

        $adjustments = iterator_to_array($this->orderItem->getAdjustmentsRecursively());

        $this->assertContains($this->itemAdjustment, $adjustments);
        $this->assertContains($this->unitAdjustment1, $adjustments);
        $this->assertContains($this->unitAdjustment2, $adjustments);

        $this->assertCount(3, $adjustments);
    }

    public function testAddsAndRemovesUnits(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(0);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->once())->method('getTotal')->willReturn(0);

        $this->assertInstanceOf(Collection::class, $this->orderItem->getUnits());

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertTrue($this->orderItem->hasUnit($this->unit1));
        $this->assertTrue($this->orderItem->hasUnit($this->unit2));

        $this->orderItem->removeUnit($this->unit1);

        $this->assertFalse($this->orderItem->hasUnit($this->unit1));
        $this->assertTrue($this->orderItem->hasUnit($this->unit2));
    }

    public function testAddsOnlyUnitThatIsAssignedToIt(): void
    {
        $this->unit1
            ->expects($this->once())
            ->method('getOrderItem')
            ->willReturn(new OrderItem())
        ;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This order item unit is assigned to a different order item.');

        $this->orderItem->addUnit($this->unit1);
    }

    public function testRecalculatesUnitsTotalOnUnitPriceChange(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(0, 100);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->atLeastOnce())->method('getTotal')->willReturn(0, 100);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);
        $this->orderItem->setUnitPrice(100);
    }

    public function testAddsAdjustmentsProperly(): void
    {
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->assertFalse($this->orderItem->hasAdjustment($this->itemAdjustment));

        $this->orderItem->addAdjustment($this->itemAdjustment);

        $this->assertTrue($this->orderItem->hasAdjustment($this->itemAdjustment));
    }

    public function testRemovesAdjustmentsProperly(): void
    {
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $this->itemAdjustment
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItem, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->assertFalse($this->orderItem->hasAdjustment($this->itemAdjustment));

        $this->orderItem->addAdjustment($this->itemAdjustment);
        $this->assertTrue($this->orderItem->hasAdjustment($this->itemAdjustment));

        $this->orderItem->removeAdjustment($this->itemAdjustment);
        $this->assertFalse($this->orderItem->hasAdjustment($this->itemAdjustment));
    }

    public function testHasCorrectTotalBasedOnUnitItems(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->once())->method('getTotal')->willReturn(1499);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->once())->method('getTotal')->willReturn(1499);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertSame(2998, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterUnitItemRemove(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(2000);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->atLeastOnce())->method('getTotal')->willReturn(1000);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertSame(3000, $this->orderItem->getTotal());

        $this->orderItem->removeUnit($this->unit2);

        $this->assertSame(2000, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterNegativeAdjustmentAdd(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->once())->method('getTotal')->willReturn(1499);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->once())->method('getTotal')->willReturn(1499);

        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(-1000);
        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);
        $this->orderItem->addAdjustment($this->itemAdjustment);

        $this->assertSame(1998, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterAdjustmentAddAndRemove(): void
    {
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(200);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $this->itemAdjustment
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItem, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->orderItem->addAdjustment($this->itemAdjustment);
        $this->assertSame(200, $this->orderItem->getTotal());

        $this->orderItem->removeAdjustment($this->itemAdjustment);
        $this->assertSame(0, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterNeutralAdjustmentAddAndRemove(): void
    {
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $this->itemAdjustment
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->orderItem, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->orderItem->addAdjustment($this->itemAdjustment);
        $this->assertSame(0, $this->orderItem->getTotal());

        $this->orderItem->removeAdjustment($this->itemAdjustment);
        $this->assertSame(0, $this->orderItem->getTotal());
    }

    public function testHas0TotalWhenAdjustmentDecreasesTotalUnder0(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->once())->method('getTotal')->willReturn(1499);

        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(-2000);
        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addAdjustment($this->itemAdjustment);

        $this->assertSame(0, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterUnitPriceChange(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(0, 100);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->atLeastOnce())->method('getTotal')->willReturn(0, 100);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);
        $this->orderItem->setUnitPrice(100);

        $this->assertSame(200, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterOrderItemUnitTotalChange(): void
    {
        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(0);

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->atLeastOnce())->method('getTotal')->willReturn(0, 100);

        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertSame(0, $this->orderItem->getTotal());

        $this->orderItem->recalculateUnitsTotal();

        $this->assertSame(100, $this->orderItem->getTotal());
    }

    public function testHasCorrectTotalAfterAdjustmentAmountChange(): void
    {
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->unitAdjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(500, 300);
        $this->unitAdjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addAdjustment($this->unitAdjustment1);
        $this->orderItem->addAdjustment($this->unitAdjustment2);

        $this->assertSame(400, $this->orderItem->getTotal());
    }

    public function testReturnsCorrectAdjustmentsTotal(): void
    {
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->unitAdjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(500);
        $this->unitAdjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addAdjustment($this->unitAdjustment1);
        $this->orderItem->addAdjustment($this->unitAdjustment2);

        $this->assertSame(600, $this->orderItem->getAdjustmentsTotal());
    }

    public function testReturnsCorrectAdjustmentsTotalByType(): void
    {
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('getType')->willReturn('tax');
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(200);
        $this->unitAdjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment1->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->unitAdjustment2->expects($this->atLeastOnce())->method('getType')->willReturn('tax');
        $this->unitAdjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(-50);
        $this->unitAdjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment2->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->unitAdjustment3->expects($this->atLeastOnce())->method('getType')->willReturn('promotion');
        $this->unitAdjustment3->expects($this->atLeastOnce())->method('getAmount')->willReturn(-1000);
        $this->unitAdjustment3->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->unitAdjustment3->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->orderItem->addAdjustment($this->unitAdjustment1);
        $this->orderItem->addAdjustment($this->unitAdjustment2);
        $this->orderItem->addAdjustment($this->unitAdjustment3);

        $this->assertSame(150, $this->orderItem->getAdjustmentsTotal('tax'));
        $this->assertSame(-1000, $this->orderItem->getAdjustmentsTotal('promotion'));
        $this->assertSame(0, $this->orderItem->getAdjustmentsTotal('any'));
    }

    public function testReturnsCorrectAdjustmentsTotalRecursively(): void
    {
        $this->itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(200);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $this->taxAdjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(150);
        $this->taxAdjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $this->taxAdjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $this->taxAdjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $this->unit1->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->once())->method('getTotal')->willReturn(500);
        $this->unit1
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(null)
            ->willReturn(new ArrayCollection([$this->taxAdjustment1]))
        ;

        $this->unit2->expects($this->once())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->once())->method('getTotal')->willReturn(300);
        $this->unit2
            ->expects($this->once())
            ->method('getAdjustments')
            ->with(null)
            ->willReturn(new ArrayCollection([$this->taxAdjustment2]))
        ;

        $this->orderItem->addAdjustment($this->itemAdjustment);
        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertSame(450, $this->orderItem->getAdjustmentsTotalRecursively());
    }

    public function testGetAdjustmentsTotalRecursively(): void
    {
        $promotionAdjustment = $this->createMock(AdjustmentInterface::class);

        $this->itemAdjustment->expects($this->atLeastOnce())->method('getType')->willReturn('tax');
        $this->itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(200);
        $this->itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->itemAdjustment->expects($this->once())->method('setAdjustable')->with($this->orderItem);

        $promotionAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(30);
        $promotionAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $this->taxAdjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(150);
        $this->taxAdjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $this->taxAdjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $this->taxAdjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $this->unit1->expects($this->atLeastOnce())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit1->expects($this->atLeastOnce())->method('getTotal')->willReturn(500);
        $this->unit1
            ->expects($this->atLeastOnce())
            ->method('getAdjustments')
            ->willReturnMap([
                ['tax', new ArrayCollection([$this->taxAdjustment1])],
                ['promotion', new ArrayCollection([$promotionAdjustment])],
            ])
        ;

        $this->unit2->expects($this->atLeastOnce())->method('getOrderItem')->willReturn($this->orderItem);
        $this->unit2->expects($this->atLeastOnce())->method('getTotal')->willReturn(300);
        $this->unit2
            ->expects($this->atLeastOnce())
            ->method('getAdjustments')
            ->willReturnMap([
                ['tax', new ArrayCollection([$this->taxAdjustment2])],
                ['promotion', new ArrayCollection()],
            ])
        ;

        $this->orderItem->addAdjustment($this->itemAdjustment);
        $this->orderItem->addUnit($this->unit1);
        $this->orderItem->addUnit($this->unit2);

        $this->assertSame(450, $this->orderItem->getAdjustmentsTotalRecursively('tax'));
        $this->assertSame(30, $this->orderItem->getAdjustmentsTotalRecursively('promotion'));
    }

    public function testCanBeImmutable(): void
    {
        $this->orderItem->setImmutable(true);
        $this->assertTrue($this->orderItem->isImmutable());
    }
}

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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Resource\Model\TimestampableInterface;

final class OrderTest extends TestCase
{
    private Order $order;

    private MockObject&OrderItemInterface $item1;

    private MockObject&OrderItemInterface $item2;

    private MockObject&OrderItemInterface $item3;

    private AdjustmentInterface&MockObject $adjustment1;

    private AdjustmentInterface&MockObject $adjustment2;

    protected function setUp(): void
    {
        $this->order = new Order();
        $this->item1 = $this->createMock(OrderItemInterface::class);
        $this->item2 = $this->createMock(OrderItemInterface::class);
        $this->item3 = $this->createMock(OrderItemInterface::class);
        $this->adjustment1 = $this->createMock(AdjustmentInterface::class);
        $this->adjustment2 = $this->createMock(AdjustmentInterface::class);
    }

    public function testImplementsAnOrderInterface(): void
    {
        $this->assertInstanceOf(OrderInterface::class, $this->order);
    }

    public function testImplementsAnAdjustableInterface(): void
    {
        $this->assertInstanceOf(AdjustableInterface::class, $this->order);
    }

    public function testImplementsATimestampableInterface(): void
    {
        $this->assertInstanceOf(TimestampableInterface::class, $this->order);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->order->getId());
    }

    public function testDoesNotHaveCompletedCheckoutByDefault(): void
    {
        $this->assertFalse($this->order->isCheckoutCompleted());
    }

    public function testItsCheckoutCanBeCompleted(): void
    {
        $this->order->completeCheckout();
        $this->assertTrue($this->order->isCheckoutCompleted());
    }

    public function testHasCheckoutCompletedWhenCompletionDateIsSet(): void
    {
        $this->assertFalse($this->order->isCheckoutCompleted());
        $this->order->setCheckoutCompletedAt(new \DateTime('2 days ago'));
        $this->assertTrue($this->order->isCheckoutCompleted());
    }

    public function testHasNoCheckoutCompletionDateByDefault(): void
    {
        $this->assertNull($this->order->getCheckoutCompletedAt());
    }

    public function testItsCheckoutCompletionDateIsMutable(): void
    {
        $date = new \DateTime('1 hour ago');

        $this->order->setCheckoutCompletedAt($date);
        $this->assertSame($date, $this->order->getCheckoutCompletedAt());
    }

    public function testHasNoNumberByDefault(): void
    {
        $this->assertNull($this->order->getNumber());
    }

    public function testItsNumberIsMutable(): void
    {
        $this->order->setNumber('001351');
        $this->assertSame('001351', $this->order->getNumber());
    }

    public function testAddsItemsProperly(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(420);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->assertFalse($this->order->hasItem($this->item1));

        $this->order->addItem($this->item1);

        $this->assertTrue($this->order->hasItem($this->item1));
    }

    public function testRemovesItemsProperly(): void
    {
        $this->item1->expects($this->exactly(2))->method('getTotal')->willReturn(420);

        $callIndex = 0;
        $this->item1
            ->expects($this->exactly(2))
            ->method('setOrder')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertInstanceOf(OrderInterface::class, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->order->addItem($this->item1);
        $this->order->removeItem($this->item1);

        $this->assertFalse($this->order->hasItem($this->item1));
    }

    public function testHasItemsTotalEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->order->getItemsTotal());
    }

    public function testCalculatesCorrectItemsTotal(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(29999);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->item2->expects($this->once())->method('getTotal')->willReturn(45000);
        $this->item2->expects($this->once())->method('setOrder')->with($this->order);

        $this->item3->expects($this->once())->method('getTotal')->willReturn(250);
        $this->item3->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);
        $this->order->addItem($this->item3);

        $this->assertSame(75249, $this->order->getItemsTotal());
    }

    public function testAddsAdjustmentsProperly(): void
    {
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);

        $this->assertFalse($this->order->hasAdjustment($this->adjustment1));

        $this->order->addAdjustment($this->adjustment1);

        $this->assertTrue($this->order->hasAdjustment($this->adjustment1));
    }

    public function testAddsAdjustmentsAndRecalculatesThemProperly(): void
    {
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);

        $this->assertFalse($this->order->hasAdjustment($this->adjustment1));

        $this->order->addAdjustment($this->adjustment1);

        $this->assertTrue($this->order->hasAdjustment($this->adjustment1));
        $this->assertSame(100, $this->order->getAdjustmentsTotal());
    }

    public function testRemovesAdjustmentsProperly(): void
    {
        $this->assertFalse($this->order->hasAdjustment($this->adjustment1));

        $callIndex = 0;
        $this->adjustment1
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertSame($this->order, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->once())->method('isLocked')->willReturn(false);

        $this->order->addAdjustment($this->adjustment1);
        $this->assertTrue($this->order->hasAdjustment($this->adjustment1));

        $this->order->removeAdjustment($this->adjustment1);

        $this->assertFalse($this->order->hasAdjustment($this->adjustment1));
        $this->assertSame(0, $this->order->getAdjustmentsTotal());
    }

    public function testRemovesAdjustmentsRecursivelyProperly(): void
    {
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $this->item1->expects($this->once())->method('getTotal')->willReturn(666);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addAdjustment($this->adjustment1);
        $this->order->addItem($this->item1);

        $this->item1->expects($this->once())->method('removeAdjustmentsRecursively')->with(null);

        $this->order->removeAdjustmentsRecursively();

        $this->assertFalse($this->order->hasAdjustment($this->adjustment1));
        $this->assertSame(0, $this->order->getAdjustmentsTotal());
    }

    public function testRemovesAdjustmentsRecursivelyByTypeProperly(): void
    {
        $orderPromotionAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderTaxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderPromotionAdjustment->expects($this->atLeastOnce())->method('getType')->willReturn('promotion');
        $orderPromotionAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $orderPromotionAdjustment
            ->expects($this->exactly(1))
            ->method('setAdjustable')
            ->with($this->isInstanceOf(Order::class));

        $orderTaxAdjustment->expects($this->once())->method('getType')->willReturn('tax');
        $orderTaxAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $orderTaxAdjustment->expects($this->atLeastOnce())->method('isLocked')->willReturn(false);

        $callIndex = 0;
        $orderTaxAdjustment
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndex) {
                if ($callIndex === 0) {
                    $this->assertInstanceOf(Order::class, $arg);
                } elseif ($callIndex === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndex;

                return true;
            }));

        $this->item1->expects($this->once())->method('getTotal')->willReturn(666);
        $this->item1
            ->expects($this->once())
            ->method('removeAdjustmentsRecursively')
            ->with('tax');

        $this->order->addAdjustment($orderPromotionAdjustment);
        $this->order->addAdjustment($orderTaxAdjustment);
        $this->order->addItem($this->item1);

        $this->order->removeAdjustmentsRecursively('tax');

        $this->assertTrue($this->order->hasAdjustment($orderPromotionAdjustment));
        $this->assertFalse($this->order->hasAdjustment($orderTaxAdjustment));
        $this->assertSame(0, $this->order->getAdjustmentsTotal('tax'));
    }

    public function testReturnsAdjustmentsRecursively(): void
    {
        $itemAdjustment1Mock = $this->createMock(AdjustmentInterface::class);
        $itemAdjustment2Mock = $this->createMock(AdjustmentInterface::class);

        $this->item1->expects($this->once())->method('setOrder')->with($this->order);
        $this->item1->expects($this->once())->method('getTotal')->willReturn(100);
        $this->item1
            ->expects($this->once())
            ->method('getAdjustmentsRecursively')
            ->with(null)
            ->willReturn(new ArrayCollection([$itemAdjustment1Mock]))
        ;

        $this->item2->expects($this->once())->method('setOrder')->with($this->order);
        $this->item2->expects($this->once())->method('getTotal')->willReturn(100);
        $this->item2
            ->expects($this->once())
            ->method('getAdjustmentsRecursively')
            ->with(null)
            ->willReturn(new ArrayCollection([$itemAdjustment2Mock]))
        ;

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addAdjustment($this->adjustment1);

        $result = $this->order->getAdjustmentsRecursively();

        $this->assertContains($this->adjustment1, $result);
        $this->assertContains($itemAdjustment1Mock, $result);
        $this->assertContains($itemAdjustment2Mock, $result);
        $this->assertCount(3, $result);
    }

    public function testHasAdjustmentsTotalEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->order->getAdjustmentsTotal());
    }

    public function testCalculatesCorrectAdjustmentsTotal(): void
    {
        $adjustment3 = $this->createMock(AdjustmentInterface::class);

        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(10000);
        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(-4999);
        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->order);

        $adjustment3->expects($this->atLeastOnce())->method('getAmount')->willReturn(1929);
        $adjustment3->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $adjustment3->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addAdjustment($this->adjustment1);
        $this->order->addAdjustment($this->adjustment2);
        $this->order->addAdjustment($adjustment3);

        $this->assertSame(6930, $this->order->getAdjustmentsTotal());
    }

    public function testReturnsAdjustmentsTotalRecursively(): void
    {
        $itemAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderAdjustment = $this->createMock(AdjustmentInterface::class);

        $itemAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(10000);

        $orderAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(5000);

        $itemAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);

        $orderAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $orderAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->item1
            ->expects($this->once())
            ->method('getAdjustmentsRecursively')
            ->with(null)
            ->willReturn(new ArrayCollection([$itemAdjustment]))
        ;
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);
        $this->item1->expects($this->once())->method('getTotal')->willReturn(15000);

        $this->order->addItem($this->item1);
        $this->order->addAdjustment($orderAdjustment);

        $this->assertSame(15000, $this->order->getAdjustmentsTotalRecursively());
    }

    public function testHasTotalEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->order->getTotal());
    }

    public function testHasTotalQuantity(): void
    {
        $this->item1->expects($this->once())->method('getQuantity')->willReturn(10);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);
        $this->item1->expects($this->once())->method('getTotal')->willReturn(500);

        $this->item2->expects($this->once())->method('getQuantity')->willReturn(30);
        $this->item2->expects($this->once())->method('setOrder')->with($this->order);
        $this->item2->expects($this->once())->method('getTotal')->willReturn(1000);

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);

        $this->assertSame(40, $this->order->getTotalQuantity());
    }

    public function testCalculatesCorrectTotal(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(29999);

        $this->item2->expects($this->once())->method('getTotal')->willReturn(45000);

        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->item2->expects($this->once())->method('setOrder')->with($this->order);

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(10000);

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(-4999);

        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);
        $this->order->addAdjustment($this->adjustment1);
        $this->order->addAdjustment($this->adjustment2);

        $this->assertSame(80000, $this->order->getTotal());
    }

    public function testCalculatesCorrectTotalAfterItemsAndAdjustmentsChanges(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(29999);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->item2->expects($this->exactly(2))->method('getTotal')->willReturn(45000);

        $callIndexItem2 = 0;
        $this->item2
            ->expects($this->exactly(2))
            ->method('setOrder')
            ->with($this->callback(function ($arg) use (&$callIndexItem2) {
                if ($callIndexItem2 === 0) {
                    $this->assertSame($arg instanceof OrderInterface ? $arg : null, $arg);
                } elseif ($callIndexItem2 === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndexItem2;

                return true;
            }));

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(10000);
        $this->adjustment1->expects($this->once())->method('isLocked')->willReturn(false);

        $callIndexAdj1 = 0;
        $this->adjustment1
            ->expects($this->exactly(2))
            ->method('setAdjustable')
            ->with($this->callback(function ($arg) use (&$callIndexAdj1) {
                if ($callIndexAdj1 === 0) {
                    $this->assertInstanceOf(Order::class, $arg);
                } elseif ($callIndexAdj1 === 1) {
                    $this->assertNull($arg);
                }
                ++$callIndexAdj1;

                return true;
            }));

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);
        $this->order->addAdjustment($this->adjustment1);

        $this->assertSame(84999, $this->order->getTotal());

        $this->order->removeItem($this->item2);
        $this->order->removeAdjustment($this->adjustment1);

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(-4999);
        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addAdjustment($this->adjustment2);

        $this->item3->expects($this->once())->method('getTotal')->willReturn(55000);
        $this->item3->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addItem($this->item3);

        $this->assertSame(80000, $this->order->getTotal());
    }

    public function testIgnoresNeutralAdjustmentsWhenCalculatingTotal(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(29999);

        $this->item2->expects($this->once())->method('getTotal')->willReturn(45000);

        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->item2->expects($this->once())->method('setOrder')->with($this->order);

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);

        $this->adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(-4999);

        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->adjustment2->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addItem($this->item1);
        $this->order->addItem($this->item2);
        $this->order->addAdjustment($this->adjustment1);
        $this->order->addAdjustment($this->adjustment2);

        $this->assertSame(70000, $this->order->getTotal());
    }

    public function testCalculatesCorrectTotalWhenAdjustmentIsBiggerThanCost(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(45000);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $this->adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(-100000);
        $this->adjustment1->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addItem($this->item1);
        $this->order->addAdjustment($this->adjustment1);

        $this->assertSame(0, $this->order->getTotal());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->order->getUpdatedAt());
    }

    public function testEmptyByDefault(): void
    {
        $this->assertSame(0, $this->order->countItems());
        $this->assertTrue($this->order->isEmpty());
    }

    public function testClearsItems(): void
    {
        $this->item1->expects($this->once())->method('getTotal')->willReturn(420);
        $this->item1->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addItem($this->item1);
        $this->order->clearItems();

        $this->assertTrue($this->order->isEmpty());
        $this->assertSame(0, $this->order->getTotal());
    }

    public function testHasNotes(): void
    {
        $this->order->setNotes('something squishy');
        $this->assertSame('something squishy', $this->order->getNotes());
    }
}

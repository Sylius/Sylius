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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class AdjustmentTest extends TestCase
{
    private Adjustment $adjustment;

    private MockObject&OrderInterface $order;

    private MockObject&OrderItemInterface $orderItem;

    private MockObject&OrderItemUnitInterface $orderItemUnit;

    protected function setUp(): void
    {
        $this->adjustment = new Adjustment();
        $this->order = $this->createMock(OrderInterface::class);
        $this->orderItem = $this->createMock(OrderItemInterface::class);
        $this->orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
    }

    public function testImplementsAnAdjustmentInterface(): void
    {
        $this->assertInstanceOf(AdjustmentInterface::class, $this->adjustment);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->adjustment->getId());
    }

    public function testDoesNotBelongToAnAdjustableByDefault(): void
    {
        $this->assertNull($this->adjustment->getAdjustable());
    }

    public function testAllowsAssigningItselfToAnAdjustable(): void
    {
        $this->order->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->order);

        $this->assertSame($this->order, $this->adjustment->getAdjustable());

        $this->order->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->orderItem->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItem);

        $this->assertSame($this->orderItem, $this->adjustment->getAdjustable());
    }

    public function testAllowsDetachingItselfFromAnAdjustable(): void
    {
        $this->order->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->order);

        $this->assertSame($this->order, $this->adjustment->getAdjustable());
        $this->assertSame($this->order, $this->adjustment->getOrder());

        $this->order->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable(null);

        $this->assertNull($this->adjustment->getAdjustable());
        $this->assertNull($this->adjustment->getOrder());

        $this->orderItem->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItem);

        $this->assertSame($this->orderItem, $this->adjustment->getAdjustable());
        $this->assertSame($this->orderItem, $this->adjustment->getOrderItem());

        $this->orderItem->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable(null);

        $this->assertNull($this->adjustment->getAdjustable());
        $this->assertNull($this->adjustment->getOrderItem());

        $this->orderItemUnit->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItemUnit);

        $this->assertSame($this->orderItemUnit, $this->adjustment->getAdjustable());
        $this->assertSame($this->orderItemUnit, $this->adjustment->getOrderItemUnit());

        $this->orderItemUnit->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable(null);

        $this->assertNull($this->adjustment->getAdjustable());
        $this->assertNull($this->adjustment->getOrderItemUnit());
    }

    public function testThrowsAnExceptionDuringNotSupportedAdjustableClassSet(): void
    {
        /** @var AdjustableInterface&MockObject $adjustableMock */
        $adjustableMock = $this->createMock(AdjustableInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->adjustment->setAdjustable($adjustableMock);
    }

    public function testThrowsAnExceptionDuringAdjustableChangeOnLockedAdjustment(): void
    {
        /** @var OrderItemInterface&MockObject $otherOrderItemMock */
        $otherOrderItemMock = $this->createMock(OrderItemInterface::class);

        $this->adjustment->setAdjustable($this->orderItem);
        $this->adjustment->lock();

        $this->expectException(\LogicException::class);

        $this->adjustment->setAdjustable($otherOrderItemMock);
    }

    public function testHasNoTypeByDefault(): void
    {
        $this->assertNull($this->adjustment->getType());
    }

    public function testItsTypeIsMutable(): void
    {
        $this->adjustment->setType('some type');
        $this->assertSame('some type', $this->adjustment->getType());
    }

    public function testHasNoLabelByDefault(): void
    {
        $this->assertNull($this->adjustment->getLabel());
    }

    public function testItsLabelIsMutable(): void
    {
        $this->adjustment->setLabel('Clothing tax (12%)');
        $this->assertSame('Clothing tax (12%)', $this->adjustment->getLabel());
    }

    public function testHasAmountEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->adjustment->getAmount());
    }

    public function testItsAmountIsMutable(): void
    {
        $this->adjustment->setAmount(399);
        $this->assertSame(399, $this->adjustment->getAmount());
    }

    public function testRecalculatesAdjustmentsOnAdjustableEntityOnAmountChange(): void
    {
        $this->order->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->order);

        $this->order->expects($this->once())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setAmount(200);

        $this->order->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->orderItem->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItem);

        $this->orderItem->expects($this->once())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setAmount(300);

        $this->orderItem->expects($this->once())->method('removeAdjustment')->with($this->adjustment);

        $this->orderItemUnit->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItemUnit);

        $this->orderItemUnit->expects($this->once())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setAmount(400);
    }

    public function testDoesNotRecalculateAdjustmentsOnAdjustableEntityOnAmountChangeWhenAdjustmentIsNeutral(): void
    {
        $this->orderItemUnit->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItemUnit);

        $this->orderItemUnit->expects($this->once())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setNeutral(true);
        $this->adjustment->setAmount(400);
    }

    public function testItsAmountShouldAcceptOnlyInteger(): void
    {
        $this->adjustment->setAmount(4498);
        $this->assertSame(4498, $this->adjustment->getAmount());
    }

    public function testNotNeutralByDefault(): void
    {
        $this->assertFalse($this->adjustment->isNeutral());
    }

    public function testItsNeutralityIsMutable(): void
    {
        $this->assertFalse($this->adjustment->isNeutral());

        $this->adjustment->setNeutral(true);

        $this->assertTrue($this->adjustment->isNeutral());
    }

    public function testRecalculateAdjustmentsOnAdjustableEntityOnNeutralChange(): void
    {
        $this->orderItemUnit->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItemUnit);

        $this->orderItemUnit->expects($this->once())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setNeutral(true);
    }

    public function testDoesNotRecalculateAdjustmentsOnAdjustableEntityWhenNeutralSetToCurrentValue(): void
    {
        $this->orderItem->expects($this->once())->method('addAdjustment')->with($this->adjustment);

        $this->adjustment->setAdjustable($this->orderItem);

        $this->orderItem->expects($this->never())->method('recalculateAdjustmentsTotal');

        $this->adjustment->setNeutral(false);
    }

    public function testAChargeIfAmountIsLesserThan0(): void
    {
        $this->adjustment->setAmount(-499);
        $this->assertTrue($this->adjustment->isCharge());

        $this->adjustment->setAmount(699);
        $this->assertFalse($this->adjustment->isCharge());
    }

    public function testACreditIfAmountIsGreaterThan0(): void
    {
        $this->adjustment->setAmount(2999);
        $this->assertTrue($this->adjustment->isCredit());

        $this->adjustment->setAmount(-299);
        $this->assertFalse($this->adjustment->isCredit());
    }

    public function testItsOriginCodeIsMutable(): void
    {
        $this->adjustment->setOriginCode('TEST_PROMOTION');
        $this->assertSame('TEST_PROMOTION', $this->adjustment->getOriginCode());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->adjustment->getUpdatedAt());
    }

    public function testItsDetailsAreMutable(): void
    {
        $this->adjustment->setDetails(['taxRateAmount' => 0.1]);
        $this->assertSame(['taxRateAmount' => 0.1], $this->adjustment->getDetails());
    }

    public function testResetsInternalInformationWhileCloning(): void
    {
        $this->adjustment->setUpdatedAt(new \DateTime());
        $new = clone $this->adjustment;

        $this->assertNotSame($new->getCreatedAt(), $this->adjustment->getCreatedAt());
        $this->assertNotSame($new->getUpdatedAt(), $this->adjustment->getUpdatedAt());
    }
}

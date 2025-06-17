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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;

final class OrderItemUnitTest extends TestCase
{
    public function testTotalWhenThereAreNoAdjustments(): void
    {
        $oi = new OrderItem();
        $oi->setUnitPrice(400);
        $oiu = new OrderItemUnit($oi);

        $this->assertSame(400, $oiu->getTotal());
    }

    public function testIncludesNonNeutralAdjustmentsInTotal(): void
    {
        $adjustment = new Adjustment();
        $adjustment->setNeutral(false);
        $adjustment->setAmount(400);

        $oiu = new OrderItemUnit(new OrderItem());
        $oiu->addAdjustment($adjustment);

        $this->assertSame(400, $oiu->getTotal());
    }

    public function testReturns0AsTotalEvenWhenAdjustmentsDecreasesItBelow0(): void
    {
        $adjustment = new Adjustment();
        $adjustment->setNeutral(false);
        $adjustment->setAmount(-1400);

        $oiu = new OrderItemUnit(new OrderItem());
        $oiu->addAdjustment($adjustment);

        $this->assertSame(0, $oiu->getTotal());
    }

    public function testAddsAndRemovesAdjustments(): void
    {
        $adjustment = new Adjustment();
        $oiu = new OrderItemUnit(new OrderItem());

        $oiu->addAdjustment($adjustment);
        $this->assertTrue($oiu->hasAdjustment($adjustment));

        $oiu->removeAdjustment($adjustment);
        $this->assertFalse($oiu->hasAdjustment($adjustment));
    }

    public function testDoesNotRemoveAdjustmentWhenItIsLocked(): void
    {
        $adjustment = new Adjustment();
        $oiu = new OrderItemUnit(new OrderItem());
        $oiu->addAdjustment($adjustment);

        $adjustment->lock();
        $this->assertTrue($oiu->hasAdjustment($adjustment));

        $oiu->removeAdjustment($adjustment);
        $this->assertTrue($oiu->hasAdjustment($adjustment));
    }

    public function testHasCorrectTotalAfterAdjustmentAddAndRemove(): void
    {
        $adjustment1 = new Adjustment();
        $adjustment1->setAmount(100);

        $adjustment2 = new Adjustment();
        $adjustment2->setAmount(50);

        $adjustment3 = new Adjustment();
        $adjustment3->setAmount(250);

        $oi = new OrderItem();
        $oi->setUnitPrice(1000);
        $oiu = new OrderItemUnit($oi);

        $oiu->addAdjustment($adjustment1);
        $oiu->addAdjustment($adjustment2);
        $this->assertSame(1000 + 100 + 50, $oiu->getTotal());

        $oiu->addAdjustment($adjustment3);
        $oiu->removeAdjustment($adjustment1);
        $this->assertSame(1000 + 50 + 250, $oiu->getTotal());
    }

    public function testHasCorrectTotalAfterNeutralAdjustmentAddAndRemove(): void
    {
        $adjustment = new Adjustment();
        $adjustment->setNeutral(true);
        $oi = new OrderItem();
        $oi->setUnitPrice(1000);
        $oiu = new OrderItemUnit($oi);

        $oiu->addAdjustment($adjustment);
        $this->assertSame(1000, $oiu->getTotal());

        $oiu->removeAdjustment($adjustment);
        $this->assertSame(1000, $oiu->getTotal());
    }

    public function testHasProperTotalAfterOrderItemUnitPriceChange(): void
    {
        $adjustment = new Adjustment();
        $adjustment->setAmount(50);
        $oi = new OrderItem();
        $oi->setUnitPrice(1000);
        $oiu = new OrderItemUnit($oi);

        $oiu->addAdjustment($adjustment);
        $this->assertSame(1050, $oiu->getTotal());

        $oi->setUnitPrice(500);
        $this->assertSame(550, $oiu->getTotal());
    }
}

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

namespace Tests\Sylius\Component\Core\Promotion\Modifier;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class OrderPromotionsUsageModifierTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private MockObject&PromotionInterface $firstPromotion;

    private MockObject&PromotionInterface $secondPromotion;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private OrderPromotionsUsageModifier $orderPromotionsUsageModifier;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstPromotion = $this->createMock(PromotionInterface::class);
        $this->secondPromotion = $this->createMock(PromotionInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->orderPromotionsUsageModifier = new OrderPromotionsUsageModifier();
    }

    public function testShouldImplementOrderPromotionsUsageModifierInterface(): void
    {
        $this->assertInstanceOf(OrderPromotionsUsageModifierInterface::class, $this->orderPromotionsUsageModifier);
    }

    public function testShouldIncrementUsageOfPromotionsAppliedOnOrder(): void
    {
        $this->order->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([
            $this->firstPromotion,
            $this->secondPromotion,
        ]));
        $this->order->expects($this->once())->method('getPromotionCoupon')->willReturn(null);
        $this->firstPromotion->expects($this->once())->method('incrementUsed');
        $this->secondPromotion->expects($this->once())->method('incrementUsed');

        $this->orderPromotionsUsageModifier->increment($this->order);
    }

    public function testShouldDecrementUsageOfPromotionsAppliedOnOrder(): void
    {
        $this->order->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([
            $this->firstPromotion,
            $this->secondPromotion,
        ]));
        $this->order->expects($this->once())->method('getPromotionCoupon')->willReturn(null);
        $this->firstPromotion->expects($this->once())->method('decrementUsed');
        $this->secondPromotion->expects($this->once())->method('decrementUsed');

        $this->orderPromotionsUsageModifier->decrement($this->order);
    }

    public function testShouldIncrementUsageOfPromotionsAndPromotionCouponAppliedOnOrder(): void
    {
        $this->order->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([
            $this->firstPromotion,
            $this->secondPromotion,
        ]));
        $this->order->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->firstPromotion->expects($this->once())->method('incrementUsed');
        $this->secondPromotion->expects($this->once())->method('incrementUsed');
        $this->promotionCoupon->expects($this->once())->method('incrementUsed');

        $this->orderPromotionsUsageModifier->increment($this->order);
    }

    public function testShouldDecrementUsageOfPromotionsAndPromotionCouponAppliedOnOrder(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn('cancelled');
        $this->order->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([
            $this->firstPromotion,
            $this->secondPromotion,
        ]));
        $this->order->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('isReusableFromCancelledOrders')->willReturn(true);
        $this->firstPromotion->expects($this->once())->method('decrementUsed');
        $this->secondPromotion->expects($this->once())->method('decrementUsed');
        $this->promotionCoupon->expects($this->once())->method('decrementUsed');

        $this->orderPromotionsUsageModifier->decrement($this->order);
    }

    public function testShouldDecrementUsageOfPromotionsAndDoesNotDecrementUsageOfPromotionCouponAppliedOnOrder(): void
    {
        $this->order->expects($this->once())->method('getState')->willReturn('cancelled');
        $this->order->expects($this->once())->method('getPromotions')->willReturn(new ArrayCollection([
            $this->firstPromotion,
            $this->secondPromotion,
        ]));
        $this->order->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('isReusableFromCancelledOrders')->willReturn(false);
        $this->firstPromotion->expects($this->once())->method('decrementUsed');
        $this->secondPromotion->expects($this->once())->method('decrementUsed');
        $this->promotionCoupon->expects($this->never())->method('decrementUsed');

        $this->orderPromotionsUsageModifier->decrement($this->order);
    }
}

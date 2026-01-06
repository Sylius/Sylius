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

namespace Tests\Sylius\Component\Promotion\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\PromotionCoupon;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class PromotionCouponTest extends TestCase
{
    private MockObject&PromotionInterface $promotion;

    private PromotionCoupon $promotionCoupon;

    protected function setUp(): void
    {
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->promotionCoupon = new PromotionCoupon();
    }

    public function testShouldImplementPromotionCouponInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponInterface::class, $this->promotionCoupon);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->promotionCoupon->getId());
    }

    public function testShouldNotHaveCodeByDefault(): void
    {
        $this->assertNull($this->promotionCoupon->getCode());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->promotionCoupon->setCode('PC1');

        $this->assertSame('PC1', $this->promotionCoupon->getCode());
    }

    public function testShouldNotHaveUsageLimitByDefault(): void
    {
        $this->assertNull($this->promotionCoupon->getUsageLimit());
    }

    public function testShouldUsageLimitBeMutable(): void
    {
        $this->promotionCoupon->setUsageLimit(10);

        $this->assertSame(10, $this->promotionCoupon->getUsageLimit());
    }

    public function testShouldNotHaveUsedByDefault(): void
    {
        $this->assertSame(0, $this->promotionCoupon->getUsed());
    }

    public function testShouldUsedBeMutable(): void
    {
        $this->promotionCoupon->setUsed(5);

        $this->assertSame(5, $this->promotionCoupon->getUsed());
    }

    public function testShouldUsedIncrementItself(): void
    {
        $this->promotionCoupon->incrementUsed();

        $this->assertSame(1, $this->promotionCoupon->getUsed());
    }

    public function testShouldUsedDecrementItself(): void
    {
        $this->promotionCoupon->setUsed(5);
        $this->promotionCoupon->decrementUsed();

        $this->assertSame(4, $this->promotionCoupon->getUsed());
    }

    public function testShouldNotBeAttachedToPromotionBydDefault(): void
    {
        $this->assertNull($this->promotionCoupon->getPromotion());
    }

    public function testShouldAttachItselfToPromotion(): void
    {
        $this->promotionCoupon->setPromotion($this->promotion);

        $this->assertSame($this->promotion, $this->promotionCoupon->getPromotion());
    }

    public function testShouldDetachItselfFromPromotion(): void
    {
        $this->promotionCoupon->setPromotion($this->promotion);

        $this->promotionCoupon->setPromotion(null);

        $this->assertNull($this->promotionCoupon->getPromotion());
    }
}

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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PromotionCoupon;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PromotionCouponTest extends TestCase
{
    private PromotionCoupon $promotionCoupon;

    protected function setUp(): void
    {
        $this->promotionCoupon = new PromotionCoupon();
    }

    public function testShouldImplementPromotionCouponInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponInterface::class, $this->promotionCoupon);
    }

    public function testShouldHaveNullPerCustomerUsageLimitByDefault(): void
    {
        $this->assertNull($this->promotionCoupon->getPerCustomerUsageLimit());
    }

    public function testShouldPerCustomerUsageLimitBeMutable(): void
    {
        $this->promotionCoupon->setPerCustomerUsageLimit(10);

        $this->assertSame(10, $this->promotionCoupon->getPerCustomerUsageLimit());
    }

    public function testShouldReusableFromCancelledOrdersFlagBeTrueByDefault(): void
    {
        $this->assertTrue($this->promotionCoupon->isReusableFromCancelledOrders());
    }

    public function testShouldReusableFromCancelledOrdersFlagBeMutable(): void
    {
        $this->promotionCoupon->setReusableFromCancelledOrders(false);

        $this->assertFalse($this->promotionCoupon->isReusableFromCancelledOrders());
    }
}

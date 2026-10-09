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

namespace Tests\Sylius\Component\Core\Promotion\Remover;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Remover\UnusedPromotionCouponRemover;
use Sylius\Component\Core\Promotion\Remover\UnusedPromotionCouponRemoverInterface;

final class UnusedPromotionCouponRemoverTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&PromotionInterface $promotion;

    private UnusedPromotionCouponRemover $remover;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->remover = new UnusedPromotionCouponRemover();
    }

    public function testItImplementsUnusedPromotionCouponRemoverInterface(): void
    {
        $this->assertInstanceOf(UnusedPromotionCouponRemoverInterface::class, $this->remover);
    }

    public function testItDoesNothingWhenOrderHasNoPromotionCoupon(): void
    {
        $this->order->method('getPromotionCoupon')->willReturn(null);

        $this->order->expects($this->never())->method('setPromotionCoupon');

        $this->remover->remove($this->order);
    }

    public function testItKeepsPromotionCouponWhenItsPromotionIsAppliedToOrder(): void
    {
        $this->order->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->method('getPromotion')->willReturn($this->promotion);
        $this->order->method('hasPromotion')->with($this->promotion)->willReturn(true);

        $this->order->expects($this->never())->method('setPromotionCoupon');

        $this->remover->remove($this->order);
    }

    public function testItRemovesPromotionCouponWhenItsPromotionIsNotAppliedToOrder(): void
    {
        $this->order->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->method('getPromotion')->willReturn($this->promotion);
        $this->order->method('hasPromotion')->with($this->promotion)->willReturn(false);

        $this->order->expects($this->once())->method('setPromotionCoupon')->with(null);

        $this->remover->remove($this->order);
    }

    public function testItRemovesPromotionCouponWithoutPromotion(): void
    {
        $this->order->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->method('getPromotion')->willReturn(null);

        $this->order->expects($this->never())->method('hasPromotion');
        $this->order->expects($this->once())->method('setPromotionCoupon')->with(null);

        $this->remover->remove($this->order);
    }
}

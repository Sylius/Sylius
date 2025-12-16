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

namespace Tests\Sylius\Component\Promotion\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Factory\PromotionCouponFactory;
use Sylius\Component\Promotion\Factory\PromotionCouponFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PromotionCouponFactoryTest extends TestCase
{
    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&PromotionInterface $promotion;

    /** @var FactoryInterface<PromotionCouponInterface>&MockObject */
    private FactoryInterface&MockObject $factory;

    private PromotionCouponFactory $promotionCouponFactory;

    protected function setUp(): void
    {
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->promotionCouponFactory = new PromotionCouponFactory($this->factory);
    }

    public function testShouldBeResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->promotionCouponFactory);
    }

    public function testShouldImplementCouponFactoryInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponFactoryInterface::class, $this->promotionCouponFactory);
    }

    public function testShouldCreateNewCoupon(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->promotionCoupon);

        $this->assertSame($this->promotionCoupon, $this->promotionCouponFactory->createNew());
    }

    public function testShouldThrowInvalidArgumentExceptionWhenPromotionIsNotCouponBased(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->promotion->expects($this->once())->method('getName')->willReturn('Christmas sale');
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(false);

        $this->promotionCouponFactory->createForPromotion($this->promotion);
    }

    public function testShouldCreateCouponAndAssignPromotion(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->promotionCoupon);
        $this->promotion->expects($this->once())->method('getName')->willReturn('Christmas sale');
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);
        $this->promotionCoupon->expects($this->once())->method('setPromotion')->with($this->promotion);

        $this->assertSame($this->promotionCoupon, $this->promotionCouponFactory->createForPromotion($this->promotion));
    }
}

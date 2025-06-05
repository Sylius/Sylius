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

namespace Tests\Sylius\Bundle\ApiBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityChecker;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;

final class AppliedCouponEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionEligibilityCheckerInterface $promotionChecker;

    private MockObject&PromotionCouponEligibilityCheckerInterface $promotionCouponChecker;

    private AppliedCouponEligibilityChecker $appliedCouponEligibilityChecker;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&PromotionInterface $promotion;

    private MockObject&OrderInterface $cart;

    private ChannelInterface&MockObject $firstChannel;

    private ChannelInterface&MockObject $secondChannel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->promotionChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->promotionCouponChecker = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->appliedCouponEligibilityChecker = new AppliedCouponEligibilityChecker(
            $this->promotionChecker,
            $this->promotionCouponChecker,
        );
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->cart = $this->createMock(OrderInterface::class);
        $this->firstChannel = $this->createMock(ChannelInterface::class);
        $this->secondChannel = $this->createMock(ChannelInterface::class);
    }

    public function testImplementsPromotionCouponEligibilityCheckerInterface(): void
    {
        self::assertInstanceOf(
            AppliedCouponEligibilityCheckerInterface::class,
            $this->appliedCouponEligibilityChecker,
        );
    }

    public function testReturnsFalseIfPromotionCouponIsNull(): void
    {
        $this->promotionCoupon->expects(self::never())->method('getPromotion');

        $this->promotion->expects(self::never())->method('getChannels');

        $this->promotionChecker->expects(self::never())->method('isEligible');

        $this->promotionCouponChecker->expects(self::never())->method('isEligible');

        self::assertFalse($this->appliedCouponEligibilityChecker->isEligible(null, $this->cart));
    }

    public function testReturnsFalseIfCartChannelIsNotOneOfPromotionChannels(): void
    {
        /** @var ChannelInterface&MockObject $thirdChannel */
        $thirdChannel = $this->createMock(ChannelInterface::class);

        $this->promotionCoupon->expects(self::once())
            ->method('getPromotion')
            ->willReturn($this->promotion);

        $this->promotion->expects(self::once())
            ->method('getChannels')
            ->willReturn(new ArrayCollection(
                [
                    $this->secondChannel,
                    $thirdChannel,
                ],
            ));

        $this->cart->expects(self::once())->method('getChannel')->willReturn($this->firstChannel);

        $this->promotionChecker->expects(self::never())->method('isEligible');

        $this->promotionCouponChecker->expects(self::never())->method('isEligible');

        self::assertFalse($this->appliedCouponEligibilityChecker->isEligible($this->promotionCoupon, $this->cart));
    }

    public function testReturnsFalseIfCouponIsNotEligible(): void
    {
        $this->promotionCoupon->expects(self::once())->method('getPromotion')->willReturn($this->promotion);

        $this->promotion->expects(self::once())->method('getChannels')->willReturn(new ArrayCollection([
            $this->firstChannel,
            $this->secondChannel,
        ]));

        $this->cart->expects(self::once())->method('getChannel')->willReturn($this->firstChannel);

        $this->promotionCouponChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->cart, $this->promotionCoupon)
            ->willReturn(false);

        $this->promotionChecker->expects(self::never())->method('isEligible');

        self::assertFalse($this->appliedCouponEligibilityChecker->isEligible($this->promotionCoupon, $this->cart));
    }

    public function testReturnsFalseIfPromotionIsNotEligible(): void
    {
        $this->promotionCoupon->method('getPromotion')->willReturn($this->promotion);

        $this->promotion->method('getChannels')->willReturn(new ArrayCollection([
            $this->firstChannel,
            $this->secondChannel,
        ]));

        $this->cart->method('getChannel')->willReturn($this->firstChannel);

        $this->promotionCouponChecker->method('isEligible')
            ->with($this->cart, $this->promotionCoupon)
            ->willReturn(true);

        $this->promotionChecker->method('isEligible')->with($this->cart, $this->promotion)->willReturn(false);

        self::assertFalse($this->appliedCouponEligibilityChecker->isEligible($this->promotionCoupon, $this->cart));
    }

    public function testReturnsTrueIfPromotionAndCouponAreEligible(): void
    {
        $this->promotionCoupon->method('getPromotion')->willReturn($this->promotion);

        $this->promotion->method('getChannels')->willReturn(new ArrayCollection([
            $this->firstChannel,
            $this->secondChannel,
        ]));

        $this->cart->method('getChannel')->willReturn($this->firstChannel);

        $this->promotionCouponChecker->method('isEligible')
            ->with($this->cart, $this->promotionCoupon)
            ->willReturn(true);

        $this->promotionChecker->method('isEligible')->with($this->cart, $this->promotion)->willReturn(true);

        self::assertTrue($this->appliedCouponEligibilityChecker->isEligible($this->promotionCoupon, $this->cart));
    }
}

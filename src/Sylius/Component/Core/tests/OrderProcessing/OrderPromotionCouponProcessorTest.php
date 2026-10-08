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

namespace Tests\Sylius\Component\Core\OrderProcessing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\OrderProcessing\OrderPromotionCouponProcessor;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;

final class OrderPromotionCouponProcessorTest extends TestCase
{
    private MockObject&PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker;

    private MockObject&OrderInterface $order;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private OrderPromotionCouponProcessor $orderPromotionCouponProcessor;

    protected function setUp(): void
    {
        $this->promotionCouponEligibilityChecker = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->orderPromotionCouponProcessor = new OrderPromotionCouponProcessor($this->promotionCouponEligibilityChecker);
    }

    public function testShouldImplementOrderProcessorInterface(): void
    {
        $this->assertInstanceOf(OrderProcessorInterface::class, $this->orderPromotionCouponProcessor);
    }

    public function testShouldThrowExceptionIfOrderIsNotCoreOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->orderPromotionCouponProcessor->process($this->createMock(BaseOrderInterface::class));
    }

    public function testShouldDoNothingIfOrderCannotBeProcessed(): void
    {
        $this->order->expects($this->once())->method('canBeProcessed')->willReturn(false);
        $this->order->expects($this->never())->method('getPromotionCoupon');
        $this->order->expects($this->never())->method('setPromotionCoupon');

        $this->orderPromotionCouponProcessor->process($this->order);
    }

    public function testShouldDoNothingIfOrderHasNoPromotionCoupon(): void
    {
        $this->order->method('canBeProcessed')->willReturn(true);
        $this->order->method('getPromotionCoupon')->willReturn(null);

        $this->promotionCouponEligibilityChecker->expects($this->never())->method('isEligible');
        $this->order->expects($this->never())->method('setPromotionCoupon');

        $this->orderPromotionCouponProcessor->process($this->order);
    }

    public function testShouldKeepEligiblePromotionCoupon(): void
    {
        $this->order->method('canBeProcessed')->willReturn(true);
        $this->order->method('getPromotionCoupon')->willReturn($this->promotionCoupon);

        $this->promotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->order, $this->promotionCoupon)
            ->willReturn(true)
        ;
        $this->order->expects($this->never())->method('setPromotionCoupon');

        $this->orderPromotionCouponProcessor->process($this->order);
    }

    public function testShouldRemoveIneligiblePromotionCoupon(): void
    {
        $this->order->method('canBeProcessed')->willReturn(true);
        $this->order->method('getPromotionCoupon')->willReturn($this->promotionCoupon);

        $this->promotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->order, $this->promotionCoupon)
            ->willReturn(false)
        ;
        $this->order->expects($this->once())->method('setPromotionCoupon')->with(null);

        $this->orderPromotionCouponProcessor->process($this->order);
    }
}

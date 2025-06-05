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

namespace Tests\Sylius\Bundle\ApiBundle\Assigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssigner;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

final class OrderPromotionCodeAssignerTest extends TestCase
{
    private MockObject&PromotionCouponRepositoryInterface $promotionCouponRepository;

    private MockObject&OrderProcessorInterface $orderProcessor;

    private OrderPromotionCodeAssigner $orderPromotionCodeAssigner;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        $this->promotionCouponRepository = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->orderPromotionCodeAssigner = new OrderPromotionCodeAssigner(
            $this->promotionCouponRepository,
            $this->orderProcessor,
        );
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testAppliesCouponToCart(): void
    {
        /** @var PromotionCouponInterface&MockObject $promotionCoupon */
        $promotionCoupon = $this->createMock(PromotionCouponInterface::class);

        $this->promotionCouponRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn($promotionCoupon);

        $this->cart->expects(self::once())->method('setPromotionCoupon')->with($promotionCoupon);

        $this->orderProcessor->expects(self::once())->method('process')->with($this->cart);

        $this->orderPromotionCodeAssigner->assign($this->cart, 'couponCode');
    }

    public function testDoesNotApplyCouponIfPromotionCouponCodeIsEmpty(): void
    {
        $this->promotionCouponRepository->expects(self::once())->method('findOneBy')->with(['code' => '']);

        $this->cart->expects(self::never())->method('setPromotionCoupon');

        $this->orderProcessor->expects(self::never())->method('process')->with($this->cart);

        $this->orderPromotionCodeAssigner->assign($this->cart, '');
    }

    public function testDoesNotApplyCouponIfPromotionCouponCodeIsInvalid(): void
    {
        $this->promotionCouponRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'invalidCode'])
            ->willReturn(null);

        $this->cart->expects(self::never())->method('setPromotionCoupon');

        $this->orderProcessor->expects(self::never())->method('process')->with($this->cart);

        $this->orderPromotionCodeAssigner->assign($this->cart, 'invalidCode');
    }

    public function testRemovesCouponIfPassedPromotionCouponCodeIsNull(): void
    {
        $this->promotionCouponRepository->expects(self::never())
            ->method('findOneBy')
            ->with(['code' => null]);

        $this->cart->expects(self::once())->method('setPromotionCoupon')->with(null);

        $this->orderProcessor->expects(self::once())->method('process')->with($this->cart);

        $this->orderPromotionCodeAssigner->assign($this->cart, null);
    }
}

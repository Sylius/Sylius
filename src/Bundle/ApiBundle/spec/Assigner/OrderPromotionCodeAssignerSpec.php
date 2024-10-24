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

namespace spec\Sylius\Bundle\ApiBundle\Assigner;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

final class OrderPromotionCodeAssignerSpec extends ObjectBehavior
{
    function let(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
    ) {
        $this->beConstructedWith($promotionCouponRepository, $orderProcessor);
    }

    function it_applies_coupon_to_cart(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this->assign($cart, 'couponCode');
    }

    function it_throws_exception_if_promotion_coupon_is_not_found(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
    ): void {
        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn(null);

        $cart->setPromotionCoupon(null)->shouldNotBeCalled();

        $orderProcessor->process($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('assign', [$cart, 'couponCode'])
        ;
    }

    function it_removes_coupon_if_passed_promotion_coupon_code_is_null(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
    ): void {
        $promotionCouponRepository->findOneBy(['code' => null])->shouldNotBeCalled();

        $cart->setPromotionCoupon(null)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this->assign($cart);
    }
}

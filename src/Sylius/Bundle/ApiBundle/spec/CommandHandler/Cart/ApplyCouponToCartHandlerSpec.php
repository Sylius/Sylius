<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ApplyCouponToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->beConstructedWith($orderRepository, $promotionCouponRepository, $orderProcessor);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_applies_coupon_to_cart(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion
    ): void {
        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getUsageLimit()->willReturn(null);
        $promotionCoupon->getExpiresAt()->willReturn(null);

        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->getEndsAt()->willReturn(null);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(ApplyCouponToCart::createFromData('cart', 'couponCode'));
    }

    function it_throws_exception_when_coupon_code_is_invalid(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderInterface $cart
    ): void {
        $command = new ApplyCouponToCart('couponCode');
        $command->setOrderTokenValue('cart');

        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$command]);
    }

    function it_throws_exception_when_coupon_usage_limit_has_expired(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon
    ): void {
        $command = new ApplyCouponToCart('couponCode');
        $command->setOrderTokenValue('cart');

        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getUsageLimit()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$command]);
    }

    function it_throws_exception_when_coupon_date_has_expired(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon
    ): void {
        $command = new ApplyCouponToCart('couponCode');
        $command->setOrderTokenValue('cart');

        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getUsageLimit()->willReturn(1);
        $promotionCoupon->getExpiresAt()->willReturn(new \DateTime('now -1 year'));

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$command]);
    }

    function it_throws_exception_when_promotion_date_has_expired(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion
    ): void {
        $command = new ApplyCouponToCart('couponCode');
        $command->setOrderTokenValue('cart');

        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getUsageLimit()->willReturn(1);
        $promotionCoupon->getExpiresAt()->willReturn(new \DateTime('now +1 year'));

        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->getEndsAt()->willReturn(new \DateTime('now -1 year'));

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$command]);
    }

    function it_throws_exception_when_coupon_does_not_have_promotion(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon
    ): void {
        $command = new ApplyCouponToCart('couponCode');
        $command->setOrderTokenValue('cart');

        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getUsageLimit()->willReturn(1);
        $promotionCoupon->getExpiresAt()->willReturn(new \DateTime('now +1 year'));

        $promotionCoupon->getPromotion()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$command]);
    }
}

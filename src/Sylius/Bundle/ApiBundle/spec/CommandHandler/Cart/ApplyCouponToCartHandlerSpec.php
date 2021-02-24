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
        PromotionCouponInterface $promotionCoupon
    ): void {
        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(ApplyCouponToCart::createFromData('cart', 'couponCode'));
    }

    function it_throws_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon
    ): void {
        $orderRepository->findCartByTokenValue('cart')->willReturn(null);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->shouldNotBeCalled();

        $cart->setPromotionCoupon($promotionCoupon)->shouldNotBeCalled();

        $orderProcessor->process($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [ApplyCouponToCart::createFromData('cart', 'couponCode')]);
    }

    function it_throws_exception_if_promotion_coupon_is_not_found(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart
    ): void {
        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn(null);

        $cart->setPromotionCoupon(null)->shouldNotBeCalled();

        $orderProcessor->process($cart)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [ApplyCouponToCart::createFromData('cart', 'couponCode')]);
    }

    function it_removes_coupon_if_passed_promotion_coupon_code_is_null(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart
    ): void {
        $orderRepository->findCartByTokenValue('cart')->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => null])->shouldNotBeCalled();

        $cart->setPromotionCoupon(null)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(ApplyCouponToCart::createFromData('cart', null));
    }
}

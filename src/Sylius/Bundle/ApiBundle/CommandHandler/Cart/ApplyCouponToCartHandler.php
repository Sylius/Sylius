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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use DateTime;
use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ApplyCouponToCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PromotionCouponRepositoryInterface */
    private $promotionCouponRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(ApplyCouponToCart $command): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($command->getOrderTokenValue());

        Assert::notNull($cart, 'Cart doesn\'t exist');

        /** @var PromotionCouponInterface $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $command->couponCode]);

        Assert::notNull($promotionCoupon, 'Could not find promotion coupon with given code');

        $couponUsageLimit = $promotionCoupon->getUsageLimit();

        if (null !== $couponUsageLimit) {
            Assert::greaterThan($couponUsageLimit, 0, 'Promotion coupon usage has expired');
        }

        $couponExpireDate = $promotionCoupon->getExpiresAt();

        if (null !== $couponExpireDate) {
            Assert::greaterThan($couponExpireDate, new DateTime(), 'Promotion coupon has expired');
        }

        $promotion = $promotionCoupon->getPromotion();

        Assert::notNull($promotion, 'Could not find promotion linked with this coupon');

        $promotionEndDate = $promotion->getEndsAt();

        if (null !== $promotionEndDate) {
            Assert::greaterThan($promotionEndDate, new DateTime(), 'Promotion has expired');
        }

        $cart->setPromotionCoupon($promotionCoupon);

        $this->orderProcessor->process($cart);
    }
}

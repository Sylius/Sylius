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
    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /** @var PromotionCouponRepositoryInterface $promotionCouponRepository */
    private $promotionCouponRepository;

    /** @var OrderProcessorInterface $orderProcessor */
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

        /** @var PromotionCouponInterface $promotion */
        $promotion = $this->promotionCouponRepository->findOneBy(['code' => $command->couponCode]);

        Assert::notNull($promotion, 'Could not find promotion with given code');

        $couponUsageLimit = $promotion->getUsageLimit();

        if (null !== $couponUsageLimit) {
            Assert::greaterThan($couponUsageLimit, 0, 'Promotion coupon usage has expired');
        }

        $expireDate = $promotion->getExpiresAt();

        if (null !== $expireDate) {
            Assert::greaterThan($expireDate, new DateTime(), 'Promotion coupon has expired');
        }

        $cart->setPromotionCoupon($promotion);

        $this->orderProcessor->process($cart);
    }
}

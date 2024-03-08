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

namespace Sylius\Bundle\ApiBundle\Assigner;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderPromotionCodeAssigner implements OrderPromotionCodeAssignerInterface
{
    public function __construct(
        private PromotionCouponRepositoryInterface $promotionCouponRepository,
        private OrderProcessorInterface $orderProcessor,
    ) {
    }

    public function assign(OrderInterface $cart, ?string $couponCode = null): OrderInterface
    {
        $promotionCoupon = $this->getPromotionCoupon($couponCode);

        $cart->setPromotionCoupon($promotionCoupon);

        $this->orderProcessor->process($cart);

        return $cart;
    }

    private function getPromotionCoupon(?string $code): ?PromotionCouponInterface
    {
        if ($code === null) {
            return null;
        }

        /** @var PromotionCouponInterface $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $code]);

        Assert::notNull($promotionCoupon);

        return $promotionCoupon;
    }
}

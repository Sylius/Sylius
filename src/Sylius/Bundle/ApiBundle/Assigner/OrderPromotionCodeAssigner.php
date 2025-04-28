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

final readonly class OrderPromotionCodeAssigner implements OrderPromotionCodeAssignerInterface
{
    /** @param PromotionCouponRepositoryInterface<PromotionCouponInterface> $promotionCouponRepository */
    public function __construct(
        private PromotionCouponRepositoryInterface $promotionCouponRepository,
        private OrderProcessorInterface $orderProcessor,
    ) {
    }

    public function assign(OrderInterface $cart, ?string $couponCode = null): OrderInterface
    {
        $promotionCoupon = $couponCode === null
            ? null
            : $this->promotionCouponRepository->findOneBy(['code' => $couponCode]);

        if ($couponCode !== null && $promotionCoupon === null) {
            return $cart;
        }

        $cart->setPromotionCoupon($promotionCoupon);
        $this->orderProcessor->process($cart);

        return $cart;
    }
}

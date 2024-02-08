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

namespace Sylius\Bundle\ApiBundle\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;

final class AppliedCouponEligibilityChecker implements AppliedCouponEligibilityCheckerInterface
{
    public function __construct(
        private PromotionEligibilityCheckerInterface $promotionChecker,
        private PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
    ) {
    }

    public function isEligible(?PromotionCouponInterface $promotionCoupon, OrderInterface $cart): bool
    {
        if (null === $promotionCoupon) {
            return false;
        }

        /** @var PromotionInterface $promotion */
        $promotion = $promotionCoupon->getPromotion();

        if (!$promotion->getChannels()->contains($cart->getChannel())) {
            return false;
        }

        if (!$this->promotionCouponChecker->isEligible($cart, $promotionCoupon)) {
            return false;
        }

        if (!$this->promotionChecker->isEligible($cart, $promotionCoupon->getPromotion())) {
            return false;
        }

        return true;
    }
}

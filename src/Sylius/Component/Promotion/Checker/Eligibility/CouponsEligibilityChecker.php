<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker\Eligibility;

use Sylius\Component\Promotion\Model\CouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CouponsEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
    {
        if (!$promotion->isCouponBased()) {
            throw new UnsupportedPromotionException('Only coupon based promotions can be evaluated by this checker.');
        }

        if (!$promotionSubject instanceof CouponAwarePromotionSubjectInterface) {
            return false;
        }

        if (null === $promotionSubject->getPromotionCoupon()) {
            return false;
        }

        return $promotion === $promotionSubject->getPromotionCoupon()->getPromotion();
    }
}

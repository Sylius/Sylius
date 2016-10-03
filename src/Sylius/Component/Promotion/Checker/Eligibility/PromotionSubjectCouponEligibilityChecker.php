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

use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionSubjectCouponEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var PromotionCouponEligibilityCheckerInterface
     */
    private $promotionCouponEligibilityChecker;

    /**
     * @param PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker
     */
    public function __construct(PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker)
    {
        $this->promotionCouponEligibilityChecker = $promotionCouponEligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
    {
        if (!$promotion->isCouponBased()) {
            return true;
        }

        if (!$promotionSubject instanceof PromotionCouponAwarePromotionSubjectInterface) {
            return false;
        }

        $promotionCoupon = $promotionSubject->getPromotionCoupon();
        if (null === $promotionCoupon) {
            return false;
        }

        if ($promotion !== $promotionCoupon->getPromotion()) {
            return false;
        }

        return $this->promotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon);
    }
}

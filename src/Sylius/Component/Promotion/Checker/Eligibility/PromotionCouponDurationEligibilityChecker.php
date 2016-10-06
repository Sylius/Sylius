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

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponDurationEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionCouponInterface $promotionCoupon)
    {
        $endsAt = $promotionCoupon->getExpiresAt();

        return $endsAt === null || new \DateTime() < $endsAt;
    }
}

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

namespace Sylius\Component\Promotion\Checker\Eligibility;

use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks the promotion eligibility as if the promotion was not applied to the subject yet.
 *
 * A promotion's own adjustments must not count towards its own eligibility, otherwise a promotion
 * lowering the subject total below its rule threshold would invalidate its own coupon. Adjustments
 * coming from other promotions are intentionally taken into account.
 *
 * The check itself stays side effect free: an already applied promotion is temporarily reverted
 * only to run the eligibility check and is restored afterwards, regardless of the outcome and even
 * if the inner check throws. Removing a no longer eligible promotion is the promotion processor's
 * responsibility, not this checker's.
 */
final readonly class WithoutOwnAdjustmentsPromotionEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    public function __construct(
        private PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        private PromotionApplicatorInterface $promotionApplicator,
    ) {
    }

    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion): bool
    {
        if (!$promotionSubject->hasPromotion($promotion)) {
            return $this->promotionEligibilityChecker->isEligible($promotionSubject, $promotion);
        }

        $this->promotionApplicator->revert($promotionSubject, $promotion);

        try {
            return $this->promotionEligibilityChecker->isEligible($promotionSubject, $promotion);
        } finally {
            $this->promotionApplicator->apply($promotionSubject, $promotion);
        }
    }
}

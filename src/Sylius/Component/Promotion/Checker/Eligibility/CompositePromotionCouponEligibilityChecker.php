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

namespace Sylius\Component\Promotion\Checker\Eligibility;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

final class CompositePromotionCouponEligibilityChecker implements PromotionCouponEligibilityCheckerInterface
{
    /**
     * @var PromotionCouponEligibilityCheckerInterface[]
     */
    private $promotionCouponEligibilityCheckers;

    /**
     * @param PromotionCouponEligibilityCheckerInterface[] $promotionCouponEligibilityCheckers
     */
    public function __construct(array $promotionCouponEligibilityCheckers)
    {
        Assert::notEmpty($promotionCouponEligibilityCheckers);
        Assert::allIsInstanceOf($promotionCouponEligibilityCheckers, PromotionCouponEligibilityCheckerInterface::class);

        $this->promotionCouponEligibilityCheckers = $promotionCouponEligibilityCheckers;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionCouponInterface $promotionCoupon): bool
    {
        foreach ($this->promotionCouponEligibilityCheckers as $promotionCouponEligibilityChecker) {
            if (!$promotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)) {
                return false;
            }
        }

        return true;
    }
}

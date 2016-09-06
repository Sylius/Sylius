<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var PromotionEligibilityCheckerInterface
     */
    protected $datesEligibilityChecker;

    /**
     * @var PromotionEligibilityCheckerInterface
     */
    protected $usageLimitEligibilityChecker;

    /**
     * @var PromotionEligibilityCheckerInterface
     */
    protected $couponsEligibilityChecker;

    /**
     * @var PromotionEligibilityCheckerInterface
     */
    protected $rulesEligibilityChecker;

    /**
     * @param PromotionEligibilityCheckerInterface $datesEligibilityChecker
     * @param PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker
     * @param PromotionEligibilityCheckerInterface $couponsEligibilityChecker
     * @param PromotionEligibilityCheckerInterface $rulesEligibilityChecker
     */
    public function __construct(
        PromotionEligibilityCheckerInterface $datesEligibilityChecker,
        PromotionEligibilityCheckerInterface $usageLimitEligibilityChecker,
        PromotionEligibilityCheckerInterface $couponsEligibilityChecker,
        PromotionEligibilityCheckerInterface $rulesEligibilityChecker
    ) {
        $this->datesEligibilityChecker = $datesEligibilityChecker;
        $this->usageLimitEligibilityChecker = $usageLimitEligibilityChecker;
        $this->couponsEligibilityChecker = $couponsEligibilityChecker;
        $this->rulesEligibilityChecker = $rulesEligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
    {
        if (!$this->datesEligibilityChecker->isEligible($promotionSubject, $promotion)) {
            return false;
        }

        if (!$this->usageLimitEligibilityChecker->isEligible($promotionSubject, $promotion)) {
            return false;
        }

        $eligible = $this->rulesEligibilityChecker->isEligible($promotionSubject, $promotion);
        if (!$eligible) {
            return false;
        }

        if (!$promotion->isCouponBased()) {
            return $eligible;
        }

        return $this->couponsEligibilityChecker->isEligible($promotionSubject, $promotion);
    }
}

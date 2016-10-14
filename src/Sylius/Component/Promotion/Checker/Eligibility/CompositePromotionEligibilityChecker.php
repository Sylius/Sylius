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

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositePromotionEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var PromotionEligibilityCheckerInterface[]
     */
    private $promotionEligibilityCheckers;

    /**
     * @param PromotionEligibilityCheckerInterface[] $promotionEligibilityCheckers
     */
    public function __construct(array $promotionEligibilityCheckers)
    {
        Assert::notEmpty($promotionEligibilityCheckers);
        Assert::allIsInstanceOf($promotionEligibilityCheckers, PromotionEligibilityCheckerInterface::class);

        $this->promotionEligibilityCheckers = $promotionEligibilityCheckers;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
    {
        foreach ($this->promotionEligibilityCheckers as $promotionEligibilityChecker) {
            if (!$promotionEligibilityChecker->isEligible($promotionSubject, $promotion)) {
                return false;
            }
        }

        return true;
    }
}

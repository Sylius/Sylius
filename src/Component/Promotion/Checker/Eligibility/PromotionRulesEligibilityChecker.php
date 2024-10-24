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

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class PromotionRulesEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    public function __construct(private ServiceRegistryInterface $ruleRegistry)
    {
    }

    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion): bool
    {
        if (!$promotion->hasRules()) {
            return true;
        }

        foreach ($promotion->getRules() as $rule) {
            if (!$this->isEligibleToRule($promotionSubject, $rule)) {
                return false;
            }
        }

        return true;
    }

    private function isEligibleToRule(PromotionSubjectInterface $subject, PromotionRuleInterface $rule): bool
    {
        /** @var RuleCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($rule->getType());

        return $checker->isEligible($subject, $rule->getConfiguration());
    }
}

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

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionRulesEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $ruleRegistry;

    /**
     * @param ServiceRegistryInterface $ruleRegistry
     */
    public function __construct(ServiceRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion)
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

    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionRuleInterface $rule
     *
     * @return bool
     */
    protected function isEligibleToRule(PromotionSubjectInterface $subject, PromotionRuleInterface $rule)
    {
        /** @var RuleCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($rule->getType());

        return $checker->isEligible($subject, $rule->getConfiguration());
    }
}

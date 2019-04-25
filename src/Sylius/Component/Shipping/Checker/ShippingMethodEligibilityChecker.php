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

namespace Sylius\Component\Shipping\Checker;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /** @var ServiceRegistryInterface|null */
    private $ruleRegistry;

    /**
     * The rule registry is default null because of backwards compatibility
     * In v2 it will be mandatory
     */
    public function __construct(ServiceRegistryInterface $ruleRegistry = null)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool
    {
        if (!$this->areCategoriesEligible($subject, $method)) {
            return false;
        }

        if (!$this->areRulesEligible($subject, $method)) {
            return false;
        }

        return true;
    }

    private function areCategoriesEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool
    {
        if (!$category = $method->getCategory()) {
            return true;
        }

        $numMatches = $numShippables = 0;
        foreach ($subject->getShippables() as $shippable) {
            ++$numShippables;
            if ($category === $shippable->getShippingCategory()) {
                ++$numMatches;
            }
        }

        switch ($method->getCategoryRequirement()) {
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE:
                return 0 === $numMatches;
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY:
                return 0 < $numMatches;
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL:
                return $numShippables === $numMatches;
        }

        return false;
    }

    private function areRulesEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool
    {
        if (null === $this->ruleRegistry) {
            return true;
        }

        if (!$method->hasRules()) {
            return true;
        }

        foreach ($method->getRules() as $rule) {
            if (!$this->isEligibleToRule($subject, $rule)) {
                return false;
            }
        }

        return true;
    }

    private function isEligibleToRule(ShippingSubjectInterface $subject, ShippingMethodRuleInterface $rule): bool
    {
        /** @var RuleCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($rule->getType());

        return $checker->isEligible($subject, $rule->getConfiguration());
    }
}

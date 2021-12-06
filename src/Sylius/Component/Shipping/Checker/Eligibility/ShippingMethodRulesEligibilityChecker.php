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

namespace Sylius\Component\Shipping\Checker\Eligibility;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class ShippingMethodRulesEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    private ServiceRegistryInterface $ruleRegistry;

    public function __construct(ServiceRegistryInterface $ruleRegistry)
    {
        $this->ruleRegistry = $ruleRegistry;
    }

    public function isEligible(ShippingSubjectInterface $shippingSubject, ShippingMethodInterface $shippingMethod): bool
    {
        if (!$shippingMethod->hasRules()) {
            return true;
        }

        foreach ($shippingMethod->getRules() as $rule) {
            if (!$this->isEligibleToRule($shippingSubject, $rule)) {
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

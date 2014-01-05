<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker;

use Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Checks if shipping method rules are capable of shipping given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /**
     * Shipping rules registry.
     *
     * @var RuleCheckerRegistryInterface
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param RuleCheckerRegistryInterface $registry
     */
    public function __construct(RuleCheckerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
    {
        if (false === $this->isCategoryRequirementSatisfied($subject, $method)) {
            return false;
        }

        foreach ($method->getRules() as $rule) {
            $checker = $this->registry->getChecker($rule->getType());

            if (false === $checker->isEligible($subject, $rule->getConfiguration())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the subject satisfies the category requirement configured in the method
     *
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface $method
     * @return bool
     */
    public function isCategoryRequirementSatisfied(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
    {
        if (!$category = $method->getCategory()) {
            return true;
        }

        $numMatches = 0;
        foreach ($subject->getShippables() as $shippable) {
            if ($category == $shippable->getShippingCategory()) {
                $numMatches++;
            }
        }

        switch ($method->getCategoryRequirement()) {
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE:
                return 0 === $numMatches;
            break;
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY:
                return 0 < $numMatches;
            break;
            case ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL:
                return count($subject->getShippables()) === $numMatches;
            break;
        }
    }
}

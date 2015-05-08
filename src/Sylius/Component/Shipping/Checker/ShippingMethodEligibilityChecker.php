<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker;

use Sylius\Component\Resource\Checker\EligibilityChecker;
use Sylius\Component\Resource\Model\RuleAwareInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * Checks if shipping method rules are capable of shipping given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityChecker extends EligibilityChecker
{
    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, RuleAwareInterface $object)
    {
        if (!$this->supports($subject, $object)) {
            return false;
        }

        if (!$this->isCategoryEligible($subject, $object)) {
            return false;
        }

        foreach ($object->getRules() as $rule) {
            $checker = $this->registry->get($rule->getType());

            if (!$checker->isEligible($subject, $rule->getConfiguration())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the subject satisfies the category requirement configured in the method
     *
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface  $method
     *
     * @return bool
     */
    public function isCategoryEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
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

    /**
     * {@inheritdoc}
     */
    protected function supports($subject, $object)
    {
        if (!$subject instanceof ShippingSubjectInterface) {
            return false;
        }

        if (!$object instanceof ShippingMethodInterface) {
            return false;
        }

        return true;
    }
}

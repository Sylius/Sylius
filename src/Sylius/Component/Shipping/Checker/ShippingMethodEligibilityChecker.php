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

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
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
}

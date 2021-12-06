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

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class CategoryRequirementEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $shippingSubject, ShippingMethodInterface $shippingMethod): bool
    {
        if (!$category = $shippingMethod->getCategory()) {
            return true;
        }

        $numMatches = $numShippables = 0;
        foreach ($shippingSubject->getShippables() as $shippable) {
            ++$numShippables;
            if ($category === $shippable->getShippingCategory()) {
                ++$numMatches;
            }
        }

        switch ($shippingMethod->getCategoryRequirement()) {
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

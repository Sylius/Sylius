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

        return match ($shippingMethod->getCategoryRequirement()) {
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE => 0 === $numMatches,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY => 0 < $numMatches,
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL => $numShippables === $numMatches,
            default => false,
        };
    }
}

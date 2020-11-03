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

use const E_USER_DEPRECATED;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.8, use "%s" instead.', 'Sylius\Component\Shipping\Checker\ShippingMethodEligibilityChecker', 'Sylius\Component\Shipping\Checker\Eligibility\CompositeShippingMethodEligibilityChecker'), E_USER_DEPRECATED);

/**
 * @deprecated since Sylius 1.8. Use Sylius\Component\Shipping\Checker\Eligibility\CompositeShippingMethodEligibilityChecker instead
 *
 * @psalm-suppress DeprecatedInterface
 */
final class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
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

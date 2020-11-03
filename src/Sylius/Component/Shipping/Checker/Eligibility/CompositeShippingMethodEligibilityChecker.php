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
use Webmozart\Assert\Assert;

final class CompositeShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /** @var ShippingMethodEligibilityCheckerInterface[] */
    private $eligibilityCheckers;

    /**
     * @param ShippingMethodEligibilityCheckerInterface[] $eligibilityCheckers
     */
    public function __construct(array $eligibilityCheckers)
    {
        Assert::allIsInstanceOf($eligibilityCheckers, ShippingMethodEligibilityCheckerInterface::class);

        $this->eligibilityCheckers = $eligibilityCheckers;
    }

    public function isEligible(ShippingSubjectInterface $shippingSubject, ShippingMethodInterface $shippingMethod): bool
    {
        foreach ($this->eligibilityCheckers as $eligibilityChecker) {
            if (!$eligibilityChecker->isEligible($shippingSubject, $shippingMethod)) {
                return false;
            }
        }

        return true;
    }
}

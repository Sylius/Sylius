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

namespace Sylius\Component\Core\Shipping\Checker\Eligibility;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface as CoreShippingMethodInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Webmozart\Assert\Assert;

final class ZoneEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    public function __construct(private ZoneMatcherInterface $zoneMatcher)
    {
    }

    public function isEligible(ShippingSubjectInterface $shippingSubject, ShippingMethodInterface $shippingMethod): bool
    {
        Assert::isInstanceOf($shippingSubject, ShipmentInterface::class);
        Assert::isInstanceOf($shippingMethod, CoreShippingMethodInterface::class);

        $shippingAddress = $shippingSubject->getOrder()?->getShippingAddress();
        if (null === $shippingAddress) {
            return true;
        }

        $zones = $this->zoneMatcher->matchAll($shippingAddress, Scope::SHIPPING);
        $shippingMethodZone = $shippingMethod->getZone();

        foreach ($zones as $zone) {
            if ($zone->getCode() === $shippingMethodZone->getCode()) {
                return true;
            }
        }

        return false;
    }
}

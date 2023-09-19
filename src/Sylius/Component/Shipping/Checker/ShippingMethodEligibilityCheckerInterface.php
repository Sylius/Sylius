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

namespace Sylius\Component\Shipping\Checker;

use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface as NewShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

trigger_deprecation(
    'sylius/shipping',
    '1.8',
    'The "%s" interface is deprecated, use "%s" instead.',
    ShippingMethodEligibilityCheckerInterface::class,
    NewShippingMethodEligibilityCheckerInterface::class,
);

/**
 * @deprecated since Sylius 1.8. Use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface instead
 *
 * We extend the new interface to adhere to backwards compatibility
 */
interface ShippingMethodEligibilityCheckerInterface extends NewShippingMethodEligibilityCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $shippingSubject, ShippingMethodInterface $shippingMethod): bool;
}

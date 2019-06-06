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
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface as NewShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

@trigger_error(sprintf('The "%s" interface is deprecated since Sylius 1.7, use "%s" instead.', ShippingMethodEligibilityCheckerInterface::class, NewShippingMethodEligibilityCheckerInterface::class), E_USER_DEPRECATED);

/**
 * @deprecated since Sylius 1.7. Use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface instead
 *
 * We extend the new interface to adhere to backwards compatibility
 */
interface ShippingMethodEligibilityCheckerInterface extends NewShippingMethodEligibilityCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool;
}

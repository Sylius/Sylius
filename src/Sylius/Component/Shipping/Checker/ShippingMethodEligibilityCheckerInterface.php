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

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

interface ShippingMethodEligibilityCheckerInterface
{
    /**
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface $method
     *
     * @return bool
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool;
}

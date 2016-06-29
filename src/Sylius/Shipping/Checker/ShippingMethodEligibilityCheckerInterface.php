<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Shipping\Checker;

use Sylius\Shipping\Model\ShippingMethodInterface;
use Sylius\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ShippingMethodEligibilityCheckerInterface
{
    /**
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface  $method
     *
     * @return bool
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method);
}

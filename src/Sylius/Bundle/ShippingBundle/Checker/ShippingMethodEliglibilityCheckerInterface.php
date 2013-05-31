<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker;

use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Promotion eliglibility checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ShippingMethodEliglibilityCheckerInterface
{
    public function isEligible(ShippablesAwareInterface $shippablesAware, ShippingMethodInterface $shippingMethod);
}

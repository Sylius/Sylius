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

use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Shipping method eligibility checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ShippingMethodEligibilityCheckerInterface
{
    /**
     * Check if given shipping method supports the concrete subject.
     *
     * @param ShippingSubjectInterface $subject
     * @param ShippingMethodInterface  $method
     *
     * @return Boolean
     */
    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method);
}

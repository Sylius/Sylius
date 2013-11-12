<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * Returns are shipping methods which support given shipping subject.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface MethodsResolverInterface
{
    /**
     * Get all methods available for given shippables aware.
     *
     * @param ShippingSubjectInterface $subject
     * @param array                    $criteria
     *
     * @return ShippingMethodInterface[]
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject, array $criteria = array());
}

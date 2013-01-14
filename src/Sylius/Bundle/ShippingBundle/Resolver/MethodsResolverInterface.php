<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Resolver;

use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;

/**
 * Returns all methods which can be used to ship a shipment
 * or a set of shippables.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface MethodsResolverInterface
{
    /**
     * Get all methods available for given shippables aware.
     *
     * @param ShipmentInterface $shipment
     * @param array             $criteria
     *
     * @return Collection
     */
    public function getSupportedMethods(ShippablesAwareInterface $shippablesAware, array $criteria = array());
}

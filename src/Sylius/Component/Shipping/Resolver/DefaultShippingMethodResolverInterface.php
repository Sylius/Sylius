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

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface DefaultShippingMethodResolverInterface
{
    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShippingMethodInterface
     *
     * @throws UnresolvedDefaultShippingMethodException
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment);
}

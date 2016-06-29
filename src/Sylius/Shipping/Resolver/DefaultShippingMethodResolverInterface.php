<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Shipping\Resolver;

use Sylius\Core\Model\ShippingMethodInterface;
use Sylius\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Shipping\Model\ShipmentInterface;

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

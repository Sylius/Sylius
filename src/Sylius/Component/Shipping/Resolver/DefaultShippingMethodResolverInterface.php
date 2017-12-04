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

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

interface DefaultShippingMethodResolverInterface
{
    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShippingMethodInterface
     *
     * @throws UnresolvedDefaultShippingMethodException
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface;
}

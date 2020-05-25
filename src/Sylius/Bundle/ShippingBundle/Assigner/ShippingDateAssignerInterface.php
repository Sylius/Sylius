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

namespace Sylius\Bundle\ShippingBundle\Assigner;

use Sylius\Component\Shipping\Model\ShipmentInterface;

interface ShippingDateAssignerInterface
{
    public function assign(ShipmentInterface $shipment): void;
}

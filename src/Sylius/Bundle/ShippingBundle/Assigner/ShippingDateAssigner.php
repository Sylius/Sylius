<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\Assigner;

use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Clock\ClockInterface;

final class ShippingDateAssigner implements ShippingDateAssignerInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    public function assign(ShipmentInterface $shipment): void
    {
        $shipment->setShippedAt($this->clock->now());
    }
}

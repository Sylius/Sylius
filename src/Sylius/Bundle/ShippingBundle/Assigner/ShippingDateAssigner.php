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

use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ShippingDateAssigner implements ShippingDateAssignerInterface
{
    private DateTimeProvider $calendar;

    public function __construct(DateTimeProvider $calendar)
    {
        $this->calendar = $calendar;
    }

    public function assign(ShipmentInterface $shipment): void
    {
        $shipment->setShippedAt($this->calendar->today());
    }
}

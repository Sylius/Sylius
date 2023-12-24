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

use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ShippingDateAssigner implements ShippingDateAssignerInterface
{
    public function __construct(private DateTimeProvider|DateTimeProviderInterface $calendar)
    {
        if ($calendar instanceof DateTimeProvider) {
            trigger_deprecation(
                'sylius/shipping-bundle',
                '1.11',
                'Passing a "%s" to "%s" constructor is deprecated and will be prohibited in Sylius 2.0. Use "%s" instead.',
                DateTimeProvider::class,
                self::class,
                DateTimeProviderInterface::class,
            );
        }
    }

    public function assign(ShipmentInterface $shipment): void
    {
        if ($this->calendar instanceof DateTimeProviderInterface) {
            $shipment->setShippedAt($this->calendar->now());

            return;
        }

        $shipment->setShippedAt($this->calendar->today());
    }
}

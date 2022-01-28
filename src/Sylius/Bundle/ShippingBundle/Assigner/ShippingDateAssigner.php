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
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ShippingDateAssigner implements ShippingDateAssignerInterface
{
    public function __construct(private ?DateTimeProvider $calendar, private ?DateTimeProviderInterface $dateTimeProvider = null)
    {
        if ($this->calendar !== null) {
            @trigger_error(
                sprintf('Passing a $calendar to "%s" constructor as the first argument is deprecated since Sylius 1.11 and this argument will be removed in 2.0.', self::class),
                \E_USER_DEPRECATED
            );
        }

        if ($this->dateTimeProvider === null) {
            @trigger_error(
                sprintf('Not passing a $dateTimeProvider to %s constructor is deprecated since Sylius 1.11 and will be prohibited in Sylius 2.0.', self::class),
                \E_USER_DEPRECATED
            );
        }
    }

    public function assign(ShipmentInterface $shipment): void
    {
        if ($this->dateTimeProvider !== null) {
            $shipment->setShippedAt($this->dateTimeProvider->now());

            return;
        }

        $shipment->setShippedAt($this->calendar->today());
    }
}

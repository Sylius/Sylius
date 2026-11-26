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

namespace Sylius\Bundle\ShippingBundle\Doctrine\ORM\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Sylius\Component\Shipping\Model\ShipmentUnit;

/** @internal */
final class UnusedShipmentUnitMappingListener
{
    public function __construct(private readonly string $shipmentUnitClass)
    {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() !== ShipmentUnit::class) {
            return;
        }

        if (is_a($this->shipmentUnitClass, ShipmentUnit::class, true)) {
            return;
        }

        unset($metadata->associationMappings['shipment']);
    }
}

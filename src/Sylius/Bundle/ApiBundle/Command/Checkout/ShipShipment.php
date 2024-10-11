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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware;

#[ShipmentIdAware]
class ShipShipment
{
    public function __construct(
        public readonly mixed $shipmentId = null,
        public readonly ?string $trackingCode = null,
    ) {
    }
}

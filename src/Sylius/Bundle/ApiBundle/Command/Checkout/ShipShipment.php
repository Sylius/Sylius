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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;

/** @experimental */
class ShipShipment implements ShipmentIdAwareInterface
{
    /** @var string|null */
    public $shipmentId;

    /** @var string|null */
    public $tracking;

    public function __construct(?string $tracking = null)
    {
        $this->tracking = $tracking;
    }

    public function getShipmentId(): ?string
    {
        return $this->shipmentId;
    }

    public function setShipmentId(?string $shipmentId): void
    {
        $this->shipmentId = $shipmentId;
    }
}

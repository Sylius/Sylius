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

use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;

class ShipShipment implements ShipmentIdAwareInterface
{
    /** @var int|null */
    public $shipmentId;

    /** @var string|null */
    public $trackingCode;

    public function __construct(?string $trackingCode = null)
    {
        $this->trackingCode = $trackingCode;
    }

    public function getShipmentId(): ?int
    {
        return $this->shipmentId;
    }

    public function setShipmentId(?int $shipmentId): void
    {
        $this->shipmentId = $shipmentId;
    }
}

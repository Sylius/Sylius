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

namespace Sylius\Bundle\CoreBundle\Message;

class ResendShipmentConfirmationEmail
{
    public function __construct(private ?int $shipmentId)
    {
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

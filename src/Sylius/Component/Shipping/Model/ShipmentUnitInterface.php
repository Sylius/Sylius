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

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ShipmentUnitInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return ShipmentInterface|null
     */
    public function getShipment(): ?ShipmentInterface;

    /**
     * @param ShipmentInterface|null $shipment
     */
    public function setShipment(?ShipmentInterface $shipment): void;

    /**
     * @return ShippableInterface|null
     */
    public function getShippable(): ?ShippableInterface;
}

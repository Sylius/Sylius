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

namespace Sylius\Component\Shipping\Model;

use Sylius\Resource\Model\TimestampableTrait;

class ShipmentUnit implements ShipmentUnitInterface, \Stringable
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var ShipmentInterface|null */
    protected $shipment;

    /** @var ShippableInterface|null */
    protected $shippable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(?ShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    public function getShippable(): ?ShippableInterface
    {
        return $this->shippable;
    }

    public function setShippable(?ShippableInterface $shippable): void
    {
        $this->shippable = $shippable;
    }
}

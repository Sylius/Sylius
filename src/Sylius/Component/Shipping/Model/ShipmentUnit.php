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

use Sylius\Component\Resource\Model\TimestampableTrait;

class ShipmentUnit implements ShipmentUnitInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var ShipmentInterface */
    protected $shipment;

    /** @var ShippableInterface */
    protected $shippable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
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

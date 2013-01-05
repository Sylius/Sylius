<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shipment item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ShipmentItem implements ShipmentItemInterface
{
    protected $id;
    protected $shipment;
    protected $shippingState;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->shippingState = ShipmentItemInterface::STATE_READY;
        $this->createdAt = new \DateTime('now');
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    public function setShipment(ShipmentInterface $shipment = null)
    {
        $this->shipment = $shipment;
    }

    public function getShippingState()
    {
        return $this->shippingState;
    }

    public function setShippingState($state)
    {
        $this->shippingState = $state;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

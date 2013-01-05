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
 * Shipment model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class Shipment implements ShipmentInterface
{
    protected $id;
    protected $state;
    protected $method;
    protected $items;
    protected $tracking;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->state = ShipmentInterface::STATE_READY;
        $this->items = new ArrayCollection();
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

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod(ShippingMethodInterface $method)
    {
        $this->method = $method;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function hasItem(ShipmentItemInterface $item)
    {
        return $this->items->contains($item);
    }

    public function addItem(ShipmentItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $item->setShipment($this);
            $this->items->add($item);
        }
    }

    public function removeItem(ShipmentItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $item->setShipment(null);
            $this->items->removeElement($item);
        }
    }

    public function getTracking()
    {
        return $this->tracking;
    }

    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
    }

    public function isTracked()
    {
        return null !== $this->tracking;
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

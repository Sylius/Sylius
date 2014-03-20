<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * This model represents single shipment.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Shipment implements ShipmentInterface, TimestampableInterface
{
    /**
     * Shipment identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Shipment state.
     *
     * @var string
     */
    protected $state = ShipmentInterface::STATE_CHECKOUT;

    /**
     * Shipping method.
     *
     * @var ShippingMethodInterface
     */
    protected $method;

    /**
     * Shipment items.
     *
     * @var Collection|ShipmentItemInterface[]
     */
    protected $items;

    /**
     * Tracking code for this shipment, if any required.
     *
     * @var string
     */
    protected $tracking;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod(ShippingMethodInterface $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(ShipmentItemInterface $item)
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ShipmentItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $item->setShipment($this);
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ShipmentItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $item->setShipment(null);
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippables()
    {
        $shippables = new ArrayCollection();

        foreach ($this->items as $item) {
            $shippable = $item->getShippable();
            if (!$shippables->contains($shippable)) {
                $shippables->add($shippable);
            }
        }

        return $shippables;
    }

    /**
     * {@inheritdoc}
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * {@inheritdoc}
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isTracked()
    {
        return null !== $this->tracking;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getShippingWeight()
    {
        $weight = 0;

        foreach ($this->items as $item) {
            $weight += $item->getShippable()->getShippingWeigth();
        }

        return $weight;
    }

    public function getShippingItemCount()
    {
        return $this->items->count();
    }

    public function getShippingItemTotal()
    {
        return 0;
    }
}

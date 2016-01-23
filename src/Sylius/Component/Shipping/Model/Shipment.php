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
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Shipment implements ShipmentInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $state = ShipmentInterface::STATE_CHECKOUT;

    /**
     * @var ShippingMethodInterface
     */
    protected $method;

    /**
     * @var Collection|ShipmentItemInterface[]
     */
    protected $items;

    /**
     * @var string
     */
    protected $tracking;

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
    public function getShippingWeight()
    {
        $weight = 0;

        foreach ($this->items as $item) {
            $weight += $item->getShippable()->getShippingWeight();
        }

        return $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingVolume()
    {
        $volume = 0;

        foreach ($this->items as $item) {
            $volume += $item->getShippable()->getShippingVolume();
        }

        return $volume;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingItemCount()
    {
        return $this->items->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingItemTotal()
    {
        return 0;
    }
}

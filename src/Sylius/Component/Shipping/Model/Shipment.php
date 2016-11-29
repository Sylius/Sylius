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
    protected $state = ShipmentInterface::STATE_CART;

    /**
     * @var ShippingMethodInterface
     */
    protected $method;

    /**
     * @var Collection|ShipmentUnitInterface[]
     */
    protected $units;

    /**
     * @var string
     */
    protected $tracking;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
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
    public function setMethod(ShippingMethodInterface $method = null)
    {
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnit(ShipmentUnitInterface $unit)
    {
        return $this->units->contains($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function addUnit(ShipmentUnitInterface $unit)
    {
        if (!$this->hasUnit($unit)) {
            $unit->setShipment($this);
            $this->units->add($unit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnit(ShipmentUnitInterface $unit)
    {
        if ($this->hasUnit($unit)) {
            $unit->setShipment(null);
            $this->units->removeElement($unit);
        }
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
    public function getShippables()
    {
        $shippables = new ArrayCollection();

        foreach ($this->units as $unit) {
            $shippable = $unit->getShippable();
            if (!$shippables->contains($shippable)) {
                $shippables->add($shippable);
            }
        }

        return $shippables;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingWeight()
    {
        $weight = 0;

        foreach ($this->units as $unit) {
            $weight += $unit->getShippable()->getShippingWeight();
        }

        return $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingVolume()
    {
        $volume = 0;

        foreach ($this->units as $unit) {
            $volume += $unit->getShippable()->getShippingVolume();
        }

        return $volume;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingUnitCount()
    {
        return $this->units->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingUnitTotal()
    {
        return 0;
    }
}

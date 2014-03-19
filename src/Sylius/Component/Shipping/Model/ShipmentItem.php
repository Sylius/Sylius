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

/**
 * Shipment item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentItem implements ShipmentItemInterface
{
    /**
     * Item id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Shipment.
     *
     * @var ShipmentInterface
     */
    protected $shipment;

    /**
     * Shippable object.
     *
     * @var ShippableInterface
     */
    protected $shippable;

    /**
     * Shipping state.
     *
     * @var string
     */
    protected $shippingState = ShipmentInterface::STATE_READY;

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
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function setShipment(ShipmentInterface $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippable()
    {
        return $this->shippable;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippable(ShippableInterface $shippable)
    {
        $this->shippable = $shippable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingState($state)
    {
        $this->shippingState = $state;

        return $this;
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
}

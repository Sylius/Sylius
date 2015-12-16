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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShipmentItem implements ShipmentItemInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var ShipmentInterface
     */
    protected $shipment;

    /**
     * @var ShippableInterface
     */
    protected $shippable;

    /**
     * @var string
     */
    protected $shippingState = ShipmentInterface::STATE_READY;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
    }
}

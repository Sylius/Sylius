<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Stock item model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItem implements StockItemInterface
{
    /**
     * Stock item id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Stockable object.
     *
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * Stock location.
     *
     * @var StockLocationInterface
     */
    protected $location;

    /**
     * On hand quantity.
     *
     * @var int
     */
    protected $onHand = 0;

    /**
     * On hold quantity.
     *
     * @var int
     */
    protected $onHold = 0;

    /**
     * Stock movements.
     *
     * @var StockMovementInterface[]
     */
    protected $stockMovements;

    /**
     * Available on demand?
     *
     * @var bool
     */
    protected $availableOnDemand = false;

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
        $this->stockMovements = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
    public function getStockable()
    {
        return $this->stockable;
    }

    /**
     * {@inheritdoc}
     */
    public function setStockable(StockableInterface $stockable)
    {
        $this->stockable = $stockable;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(StockLocationInterface $location)
    {
        $this->location = $location;

        return $this;
    }

    public function getStockMovements()
    {
        return $this->stockMovements;
    }

    public function addStockMovement(StockMovementInterface $movement)
    {
        if (!$this->hasStockMovement($movement)) {
            $this->stockMovements->add($movement);
        }

        return $this;
    }

    public function removeStockMovement(StockMovementInterface $movement)
    {
        if ($this->hasStockMovement($movement)) {
            $this->stockMovements->removeElement($movement);
        }

        return $this;
    }

    public function hasStockMovement(StockMovementInterface $movement)
    {
        return $this->stockMovements->contains($movement);
    }

    public function getOnHand()
    {
        return $this->onHand;
    }

    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;

        return $this;
    }

    public function getOnHold()
    {
        return $this->onHold;
    }

    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;

        return $this;
    }

    public function isAvailableOnDemand()
    {
        return $this->availableOnDemand;
    }

    public function setAvailableOnDemand($availableOnDemand)
    {
        $this->availableOnDemand = (bool) $availableOnDemand;

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
    public function setCreatedAt(\DateTime $createAt)
    {
        $this->createdAt = $createAt;

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

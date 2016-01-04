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
use Doctrine\Common\Collections\Collection;

/**
 * Stock item model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItem implements StockItemInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * @var StockLocationInterface
     */
    protected $location;

    /**
     * @var int
     */
    protected $onHand = 0;

    /**
     * @var int
     */
    protected $onHold = 0;

    /**
     * @var Collection|StockMovementInterface[]
     */
    protected $stockMovements;

    /**
     * @var bool
     */
    protected $availableOnDemand = false;

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
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocation(StockLocationInterface $location)
    {
        $this->location = $location;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockMovements()
    {
        return $this->stockMovements;
    }

    /**
     * {@inheritdoc}
     */
    public function addStockMovement(StockMovementInterface $movement)
    {
        if (!$this->hasStockMovement($movement)) {
            $this->stockMovements->add($movement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStockMovement(StockMovementInterface $movement)
    {
        if ($this->hasStockMovement($movement)) {
            $this->stockMovements->removeElement($movement);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasStockMovement(StockMovementInterface $movement)
    {
        return $this->stockMovements->contains($movement);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableOnDemand()
    {
        return $this->availableOnDemand;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOnDemand($availableOnDemand)
    {
        $this->availableOnDemand = (bool) $availableOnDemand;
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

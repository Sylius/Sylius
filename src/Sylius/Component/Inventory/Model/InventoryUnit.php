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

/**
 * Inventory unit model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnit implements InventoryUnitInterface
{
    /**
     * Product id.
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
     * Stock item used as supply.
     *
     * @var StockItemInterface
     */
    protected $stockItem;

    /**
     * State of the inventory unit.
     *
     * @var string
     */
    protected $inventoryState = InventoryUnitInterface::STATE_CHECKOUT;

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


    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        if (null !== $this->stockItem) {
            return $this->stockItem->getLocation();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * {@inheritdoc}
     */
    public function setStockItem(StockItemInterface $stockItem = null)
    {
        $this->stockItem = $stockItem;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->stockable->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->stockable->getInventoryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryState()
    {
        return $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function setInventoryState($state)
    {
        $this->inventoryState = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSold()
    {
        return InventoryUnitInterface::STATE_SOLD === $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackordered()
    {
        return InventoryUnitInterface::STATE_BACKORDERED === $this->inventoryState;
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

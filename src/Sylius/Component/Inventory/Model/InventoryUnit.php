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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnit implements InventoryUnitInterface
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
     * @var string
     */
    protected $inventoryState = InventoryUnitInterface::STATE_CHECKOUT;

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

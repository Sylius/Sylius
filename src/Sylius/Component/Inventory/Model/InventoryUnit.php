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

use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnit implements InventoryUnitInterface
{
    use TimestampableTrait;

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
     * @param StockableInterface $stockable
     */
    public function setStockable(StockableInterface $stockable)
    {
        $this->stockable = $stockable;
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
}

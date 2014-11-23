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
 * Stockable within a StockTransfer
 *
 * @author Patrick Berenschot <p.berenschot@take-abyte.eu>
 */
class StockMovement implements StockMovementInterface
{
    /**
     * Movement id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Stockable to tranfer
     *
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * Number of stockable in movement
     *
     * @var int
     */
    protected $quantity;

    /**
     * Transfer for this movement
     *
     * @var StockTransferInterface
     */
    protected $transfer;

    /**
     * @var StockItemInterface
     */
    protected $stockItem;

    /**
     * Get the id for the stock movement
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransfer(StockTransferInterface $transfer)
    {
        $this->transfer = $transfer;

        return $this;
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
    public function setStockItem(StockItemInterface $stockItem)
    {
        $this->stockItem = $stockItem;

        return $this;
    }
}

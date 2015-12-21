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
 * Stock movement model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovement implements StockMovementInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var StockItemInterface
     */
    protected $stockItem;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

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
}

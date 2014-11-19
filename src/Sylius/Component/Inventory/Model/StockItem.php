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
 * StockItem model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItem implements StockItemInterface
{
    /**
     * Product id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Stock location
     *
     * @var StockLocationInterface
     */
    protected $location;

    /**
     * Stockable subject
     *
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * On hold.
     *
     * @var int
     */
    protected $onHold = 0;

    /**
     * On hand stock.
     *
     * @var int
     */
    protected $onHand = 0;


    /**
     * @var StockMovement
     */
    protected $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
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
    public function setStockLocation(StockLocationInterface $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function getMovements()
    {
        return $this->movements;
    }

    /**
     * {@inheritdoc}
     */
    public function addMovement(StockMovementInterface $movement)
    {
        $this->movements->add($movement);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMovement(StockMovementInterface $movement)
    {
        $this->movements->removeElement($movement);

        return $this;
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

        return $this;
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

        if (0 > $this->onHand) {
            $this->onHand = 0;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return 0 < $this->onHand;
    }
}

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
 * Location for Stockable
 *
 * @author Patrick Berenschot <p.berenschot@take-abyte.eu>
 */
class StockLocation implements StockLocationInterface
{
    /**
     * Location id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Name of the location
     *
     * @var string
     */
    protected $name;

    /**
     * Stockable object.
     *
     * @var StockableInterface
     */
    protected $stockable;

    /**
     * StockItems
     *
     * @var Collection
     */
    protected $items;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * Get the id for the stock location
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(StockItemInterface $item)
    {
        $this->items->add($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(StockItemInterface $item)
    {
        $this->items->removeElement($item);

        return $this;
    }
}

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
}

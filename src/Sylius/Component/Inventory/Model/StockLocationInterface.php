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
interface StockLocationInterface
{
    /**
     * Get related stockable object.
     *
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * Set stockable object.
     *
     * @param $stockable StockableInterface
     */
    public function setStockable(StockableInterface $stockable);

    /**
     * Get the name for this stock location
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name for this stock location
     *
     * @param $name string
     *
     * @return $this
     */
    public function setName($name);
}
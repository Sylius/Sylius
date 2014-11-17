<?php
/**
 * Created by PhpStorm.
 * User: TAB
 * Date: 11/17/2014
 * Time: 9:40 AM
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
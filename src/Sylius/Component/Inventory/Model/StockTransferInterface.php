<?php
/**
 * Created by PhpStorm.
 * User: TAB
 * Date: 11/17/2014
 * Time: 9:46 AM
 */
namespace Sylius\Component\Inventory\Model;


/**
 * Transfer of stock from a source location to the destination location
 *
 * @author Patrick Berenschot <p.berenschot@take-abyte.eu>
 */
interface StockTransferInterface
{
    /**
     * @return StockLocationInterface
     */
    public function getDestination();

    /**
     * @param StockLocationInterface $destination
     *
     * @return $this
     */
    public function setDestination(StockLocationInterface $destination);

    /**
     * @return StockLocationInterface
     */
    public function getSource();

    /**
     * @param StockLocationInterface $source
     *
     * @return $this
     */
    public function setSource(StockLocationInterface $source);
}
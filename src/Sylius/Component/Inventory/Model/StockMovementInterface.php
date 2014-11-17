<?php
/**
 * Created by PhpStorm.
 * User: TAB
 * Date: 11/17/2014
 * Time: 9:53 AM
 */
namespace Sylius\Component\Inventory\Model;


/**
 * Stockable within a StockTransfer
 *
 * @author Patrick Berenschot <p.berenschot@take-abyte.eu>
 */
interface StockMovementInterface
{
    /**
     * @return StockableInterface
     */
    public function getStockable();

    /**
     * @param StockableInterface $stockable
     *
     * @return $this
     */
    public function setStockable(StockableInterface $stockable);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @return StockTransferInterface
     */
    public function getTransfer();

    /**
     * @param StockTransferInterface $transfer
     *
     * @return $this
     */
    public function setTransfer(StockTransferInterface $transfer);
}
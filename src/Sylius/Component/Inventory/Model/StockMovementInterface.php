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

    /**
     * @return StockItemInterface
     */
    public function getStockItem();

    /**
     * @param StockItemInterface $stockItem
     *
     * @return $this
     */
    public function setStockItem(StockItemInterface $stockItem);
}
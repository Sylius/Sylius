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
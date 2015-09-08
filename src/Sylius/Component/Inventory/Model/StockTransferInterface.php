<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

/**
 * Stock transfer.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StockTransferInterface
{
    public function getSource();
    public function setSource(StockLocationInterface $source);

    public function getDestination();
    public function setDestination(StockLocationInterface $source);
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Quantifier;

use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Service implementing this interface is responsible for getting total quantity in stock for given variant.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface QuantifierInterface
{
    /**
     * Return total quantity for given stockable.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    public function getTotalOnHand(StockableInterface $stockable);

    /**
     * Return total quantity for given stockable, which is on hold.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    public function getTotalOnHold(StockableInterface $stockable);
}

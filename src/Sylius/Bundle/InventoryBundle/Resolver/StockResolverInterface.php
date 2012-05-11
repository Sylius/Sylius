<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StockingBundle\Resolver;

use Sylius\Bundle\StockingBundle\Model\StockableInterface;

/**
 * Stock resolver interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
interface StockResolverInterface
{
    /**
     * Checks whether stockable object is available in stock.
     *
     * @param StockableInterface $stockable
     * @param Boolean            $strict
     *
     * @return Boolean
     */
    function isAvailable(StockableInterface $stockable, $strict =  true);

    /**
     * Get total available inventory units for given stockable object.
     *
     * @param StockableInterface $stockable
     *
     * @return integer
     */
    function getStock(StockableInterface $stockable);
}

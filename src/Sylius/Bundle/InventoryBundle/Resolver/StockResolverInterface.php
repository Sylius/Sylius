<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Resolver;

use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

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
     *
     * @return Boolean
     */
    function isInStock(StockableInterface $stockable);
}

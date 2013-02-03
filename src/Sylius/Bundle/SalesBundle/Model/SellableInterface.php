<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Sellable item interface.
 *
 * This interface should be implemented by an object which
 * is considered as orderable item.
 *
 * Those items can be placed in orders inside orders with specified
 * quantity and unit price via the order builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SellableInterface
{
    /**
     * Get the name of item which will be displayed in orders.
     *
     * @return string
     */
    public function getSellableName();
}

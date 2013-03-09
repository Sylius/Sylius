<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Builder;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\SalesBundle\Model\SellableInterface;

/**
 * Order builder interface.
 * Implementation should support fluid interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderBuilderInterface
{
    /**
     * Creates new order instance.
     *
     * @return OrderBuilderInterface
     */
    public function create();

    /**
     * Modify existing order.
     *
     * @param OrderInterface $order
     *
     * @return OrderBuilderInterface
     */
    public function modify(OrderInterface $order);

    /**
     * Add a sellable item at specified price and with given quantity.
     *
     * @param SellableInterface $sellable
     * @param integer           $unitPrice
     * @param integer           $quantity
     *
     * @return OrderBuilderInterface
     */
    public function add(SellableInterface $sellable, $unitPrice, $quantity = 1);

    /**
     * Return final order instance.
     *
     * @return OrderInterface
     */
    public function getOrder();
}

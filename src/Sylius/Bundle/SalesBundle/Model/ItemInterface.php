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
 * Interface for order line item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemInterface
{
    /**
     * Return item id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Set item id.
     *
     * @param mixed $id
     */
    function setId($id);

    /**
     * Return order.
     *
     * @return OrderInterface
     */
    function getOrder();

    /**
     * Set order.
     *
     * @param OrderInterface $order
     */
    function setOrder(OrderInterface $order = null);

    /**
     * Get item quantity.
     *
     * @return integer
     */
    function getQuantity();

    /**
     * Set quantity.
     *
     * @param integer $quantity
     */
    function setQuantity($quantity);
}

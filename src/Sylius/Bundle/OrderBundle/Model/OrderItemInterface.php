<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Model;

/**
 * Interface for order line item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderItemInterface extends AdjustableInterface
{
    /**
     * Return order.
     *
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Set order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);

    /**
     * Get item quantity.
     *
     * @return integer
     */
    public function getQuantity();

    /**
     * Set quantity.
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity);

    /**
     * Get unit price of item.
     *
     * @return integer
     */
    public function getUnitPrice();

    /**
     * Define the unit price of item.
     *
     * @param integer $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * Get item total.
     *
     * @return integer
     */
    public function getTotal();

    /**
     * Set item total.
     *
     * @param integer $total
     */
    public function setTotal($total);

    /**
     * Calculate total based on quantity and unit price.
     * Take adjustments into account.
     */
    public function calculateTotal();

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overwritten to enable merge quantities.
     *
     * @param OrderItemInterface $orderItem
     *
     * @return Boolean
     */
    public function equals(OrderItemInterface $orderItem);

    /**
     * Merge the item given as argument corresponding to
     * the same cart item.
     *
     * @param OrderItemInterface $orderItem
     * @param bool               $throwOnInvalid
     *
     * @return $this
     */
    public function merge(OrderItemInterface $orderItem, $throwOnInvalid = true);
}

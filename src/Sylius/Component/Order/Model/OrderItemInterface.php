<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Interface for order line item model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends OrderAwareInterface, ResourceInterface
{
    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getUnitPrice();

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * @return int
     */
    public function getTotal();

    /**
     * Get the basic total of the items (excluding adjustments)
     *
     * @return int
     */
    public function getSubtotal();

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getAdjustmentsTotal($type = null);

    /**
     * Calculate items total based on quantity and unit price.
     */
    public function calculateTotal();

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overwritten to enable merge quantities.
     *
     * @param OrderItemInterface $orderItem
     *
     * @return bool
     */
    public function equals(OrderItemInterface $orderItem);

    /**
     * Merge the item given as argument corresponding to
     * the same cart item.
     *
     * @param OrderItemInterface $orderItem
     * @param bool               $throwOnInvalid
     */
    public function merge(OrderItemInterface $orderItem, $throwOnInvalid = true);

    /**
     * @return bool
     */
    public function isImmutable();

    /**
     * @param bool $immutable
     */
    public function setImmutable($immutable);
}

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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends AdjustableInterface, OrderAwareInterface, ResourceInterface
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

    /**
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getItemUnits();

    /**
     * @param OrderItemUnitInterface $itemUnit
     *
     * @return bool
     */
    public function hasItemUnit(OrderItemUnitInterface $itemUnit);

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function addItemUnit(OrderItemUnitInterface $itemUnit);

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function removeItemUnit(OrderItemUnitInterface $itemUnit);
}

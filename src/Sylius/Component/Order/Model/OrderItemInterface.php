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
     * Recalculate totals. Should be used after every unit change.
     */
    public function recalculateUnitsTotal();

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
    public function getUnits();

    /**
     * @param OrderItemUnitInterface $itemUnit
     *
     * @return bool
     */
    public function hasUnit(OrderItemUnitInterface $itemUnit);

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function addUnit(OrderItemUnitInterface $itemUnit);

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function removeUnit(OrderItemUnitInterface $itemUnit);

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getAdjustmentsRecursively($type = null);

    /**
     * @param string|null $type
     */
    public function removeAdjustmentsRecursively($type = null);

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getAdjustmentsTotalRecursively($type = null);
}

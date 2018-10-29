<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderItemInterface extends AdjustableInterface, OrderAwareInterface, ResourceInterface
{
    /**
     * @return int
     */
    public function getQuantity(): int;

    /**
     * @return int
     */
    public function getUnitPrice(): int;

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice): void;

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * Recalculate totals. Should be used after every unit change.
     */
    public function recalculateUnitsTotal(): void;

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overwritten to enable merge quantities.
     *
     * @param OrderItemInterface $orderItem
     *
     * @return bool
     */
    public function equals(OrderItemInterface $orderItem): bool;

    /**
     * @return bool
     */
    public function isImmutable(): bool;

    /**
     * @param bool $immutable
     */
    public function setImmutable(bool $immutable): void;

    /**
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getUnits(): Collection;

    /**
     * @param OrderItemUnitInterface $itemUnit
     *
     * @return bool
     */
    public function hasUnit(OrderItemUnitInterface $itemUnit): bool;

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function addUnit(OrderItemUnitInterface $itemUnit): void;

    /**
     * @param OrderItemUnitInterface $itemUnit
     */
    public function removeUnit(OrderItemUnitInterface $itemUnit): void;

    /**
     * @param string|null $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustmentsRecursively(?string $type = null): Collection;

    /**
     * @param string|null $type
     */
    public function removeAdjustmentsRecursively(?string $type = null): void;

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getAdjustmentsTotalRecursively(?string $type = null): int;
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    public function getQuantity(): int;

    public function getUnitPrice(): int;

    public function setUnitPrice(int $unitPrice): void;

    public function getOriginalUnitPrice(): ?int;

    public function setOriginalUnitPrice(?int $originalUnitPrice): void;

    public function getTotal(): int;

    /**
     * Recalculate totals. Should be used after every unit change.
     */
    public function recalculateUnitsTotal(): void;

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overwritten to enable merge quantities.
     */
    public function equals(self $orderItem): bool;

    public function isImmutable(): bool;

    public function setImmutable(bool $immutable): void;

    /**
     * @return Collection<array-key, OrderItemUnitInterface>
     */
    public function getUnits(): Collection;

    public function hasUnit(OrderItemUnitInterface $itemUnit): bool;

    public function addUnit(OrderItemUnitInterface $itemUnit): void;

    public function removeUnit(OrderItemUnitInterface $itemUnit): void;

    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function getAdjustmentsRecursively(?string $type = null): Collection;

    public function removeAdjustmentsRecursively(?string $type = null): void;

    public function getAdjustmentsTotalRecursively(?string $type = null): int;
}

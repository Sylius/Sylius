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
use Sylius\Component\Resource\Model\TimestampableInterface;

interface OrderInterface extends AdjustableInterface, ResourceInterface, TimestampableInterface
{
    public const STATE_CART = 'cart';

    public const STATE_NEW = 'new';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_FULFILLED = 'fulfilled';

    public function getCheckoutCompletedAt(): ?\DateTimeInterface;

    public function setCheckoutCompletedAt(?\DateTimeInterface $checkoutCompletedAt): void;

    public function isCheckoutCompleted(): bool;

    public function completeCheckout(): void;

    public function getNumber(): ?string;

    public function setNumber(?string $number): void;

    public function getNotes(): ?string;

    public function setNotes(?string $notes): void;

    /**
     * @return Collection<array-key, OrderItemInterface>
     */
    public function getItems(): Collection;

    public function clearItems(): void;

    public function countItems(): int;

    public function addItem(OrderItemInterface $item): void;

    public function removeItem(OrderItemInterface $item): void;

    public function hasItem(OrderItemInterface $item): bool;

    public function getItemsTotal(): int;

    public function recalculateItemsTotal(): void;

    public function getTotal(): int;

    public function getTotalQuantity(): int;

    public function getState(): string;

    public function setState(string $state): void;

    public function isEmpty(): bool;

    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function getAdjustmentsRecursively(?string $type = null): Collection;

    public function getAdjustmentsTotalRecursively(?string $type = null): int;

    public function removeAdjustmentsRecursively(?string $type = null): void;

    public function canBeProcessed(): bool;
}

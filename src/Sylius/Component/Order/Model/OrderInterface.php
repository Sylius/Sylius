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
use Sylius\Component\Resource\Model\TimestampableInterface;

interface OrderInterface extends AdjustableInterface, ResourceInterface, TimestampableInterface
{
    public const STATE_CART = 'cart';
    public const STATE_NEW = 'new';
    public const STATE_CANCELLED = 'cancelled';
    public const STATE_FULFILLED = 'fulfilled';

    /**
     * @return \DateTimeInterface|null
     */
    public function getCheckoutCompletedAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $checkoutCompletedAt
     */
    public function setCheckoutCompletedAt(?\DateTimeInterface $checkoutCompletedAt): void;

    /**
     * @return bool
     */
    public function isCheckoutCompleted(): bool;

    public function completeCheckout(): void;

    /**
     * @return string|null
     */
    public function getNumber(): ?string;

    /**
     * @param string|null $number
     */
    public function setNumber(?string $number): void;

    /**
     * @return string|null
     */
    public function getNotes(): ?string;

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void;

    /**
     * @return Collection|OrderItemInterface[]
     */
    public function getItems(): Collection;

    public function clearItems(): void;

    /**
     * @return int
     */
    public function countItems(): int;

    /**
     * @param OrderItemInterface $item
     */
    public function addItem(OrderItemInterface $item): void;

    /**
     * @param OrderItemInterface $item
     */
    public function removeItem(OrderItemInterface $item): void;

    /**
     * @param OrderItemInterface $item
     *
     * @return bool
     */
    public function hasItem(OrderItemInterface $item): bool;

    /**
     * @return int
     */
    public function getItemsTotal(): int;

    public function recalculateItemsTotal(): void;

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @return int
     */
    public function getTotalQuantity(): int;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $state
     */
    public function setState(string $state): void;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param string|null $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustmentsRecursively(?string $type = null): Collection;

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getAdjustmentsTotalRecursively(?string $type = null): int;

    /**
     * @param string|null $type
     */
    public function removeAdjustmentsRecursively(?string $type = null): void;
}

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

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ShipmentInterface extends ResourceInterface, ShippingSubjectInterface, TimestampableInterface
{
    public const STATE_CART = 'cart';

    public const STATE_READY = 'ready';

    public const STATE_SHIPPED = 'shipped';

    public const STATE_CANCELLED = 'cancelled';

    public function getState(): ?string;

    public function setState(?string $state): void;

    public function getMethod(): ?ShippingMethodInterface;

    public function setMethod(?ShippingMethodInterface $method): void;

    /**
     * @return Collection|ShipmentUnitInterface[]
     *
     * @psalm-return Collection<array-key, ShipmentUnitInterface>
     */
    public function getUnits(): Collection;

    public function addUnit(ShipmentUnitInterface $unit): void;

    public function removeUnit(ShipmentUnitInterface $unit): void;

    public function hasUnit(ShipmentUnitInterface $unit): bool;

    public function getTracking(): ?string;

    public function setTracking(?string $tracking): void;

    public function isTracked(): bool;

    public function getShippedAt(): ?\DateTimeInterface;

    public function setShippedAt(?\DateTimeInterface $shippedAt): void;
}

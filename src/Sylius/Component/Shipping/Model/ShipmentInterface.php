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

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void;

    /**
     * @return ShippingMethodInterface|null
     */
    public function getMethod(): ?ShippingMethodInterface;

    /**
     * @param ShippingMethodInterface|null $method
     */
    public function setMethod(?ShippingMethodInterface $method): void;

    /**
     * @return Collection|ShipmentUnitInterface[]
     */
    public function getUnits(): Collection;

    /**
     * @param ShipmentUnitInterface $unit
     */
    public function addUnit(ShipmentUnitInterface $unit): void;

    /**
     * @param ShipmentUnitInterface $unit
     */
    public function removeUnit(ShipmentUnitInterface $unit): void;

    /**
     * @param ShipmentUnitInterface $unit
     *
     * @return bool
     */
    public function hasUnit(ShipmentUnitInterface $unit): bool;

    /**
     * @return string|null
     */
    public function getTracking(): ?string;

    /**
     * @param string|null $tracking
     */
    public function setTracking(?string $tracking): void;

    /**
     * @return bool
     */
    public function isTracked(): bool;
}

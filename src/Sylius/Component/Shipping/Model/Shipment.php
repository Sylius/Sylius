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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Shipment implements ShipmentInterface, \Stringable
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $state = ShipmentInterface::STATE_CART;

    /** @var ShippingMethodInterface|null */
    protected $method;

    /**
     * @var Collection|ShipmentUnitInterface[]
     *
     * @psalm-var Collection<array-key, ShipmentUnitInterface>
     */
    protected $units;

    /** @var string|null */
    protected $tracking;

    /** @var \DateTimeInterface|null */
    protected $shippedAt;

    public function __construct()
    {
        /** @var ArrayCollection<array-key, ShipmentUnitInterface> $this->units */
        $this->units = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getMethod(): ?ShippingMethodInterface
    {
        return $this->method;
    }

    public function setMethod(?ShippingMethodInterface $method): void
    {
        $this->method = $method;
    }

    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function hasUnit(ShipmentUnitInterface $unit): bool
    {
        return $this->units->contains($unit);
    }

    public function addUnit(ShipmentUnitInterface $unit): void
    {
        if (!$this->hasUnit($unit)) {
            $unit->setShipment($this);
            $this->units->add($unit);
        }
    }

    public function removeUnit(ShipmentUnitInterface $unit): void
    {
        if ($this->hasUnit($unit)) {
            $unit->setShipment(null);
            $this->units->removeElement($unit);
        }
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(?string $tracking): void
    {
        $this->tracking = $tracking;
    }

    public function isTracked(): bool
    {
        return null !== $this->tracking;
    }

    public function getShippables(): Collection
    {
        /** @var ArrayCollection<array-key, ShippableInterface> $shippables */
        $shippables = new ArrayCollection();

        foreach ($this->units as $unit) {
            $shippable = $unit->getShippable();
            if (!$shippables->contains($shippable)) {
                $shippables->add($shippable);
            }
        }

        return $shippables;
    }

    public function getShippingWeight(): float
    {
        $weight = 0;

        foreach ($this->units as $unit) {
            $weight += $unit->getShippable()->getShippingWeight();
        }

        return $weight;
    }

    public function getShippingVolume(): float
    {
        $volume = 0;

        foreach ($this->units as $unit) {
            $volume += $unit->getShippable()->getShippingVolume();
        }

        return $volume;
    }

    public function getShippingUnitCount(): int
    {
        return $this->units->count();
    }

    public function getShippingUnitTotal(): int
    {
        return 0;
    }

    public function getShippedAt(): ?\DateTimeInterface
    {
        return $this->shippedAt;
    }

    public function setShippedAt(?\DateTimeInterface $shippedAt): void
    {
        $this->shippedAt = $shippedAt;
    }
}

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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

class Shipment extends BaseShipment implements ShipmentInterface
{
    /** @var BaseOrderInterface|null */
    protected $order;

    /**
     * @var Collection|AdjustmentInterface[]
     *
     * @psalm-var Collection<array-key, AdjustmentInterface>
     */
    protected $adjustments;

    /** @var int */
    protected $adjustmentsTotal = 0;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, AdjustmentInterface> $this->adjustments */
        $this->adjustments = new ArrayCollection();
    }

    public function getOrder(): ?BaseOrderInterface
    {
        return $this->order;
    }

    public function setOrder(?BaseOrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type): bool {
            return $type === $adjustment->getType();
        });
    }

    public function addAdjustment(AdjustmentInterface $adjustment): void
    {
        if ($this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->add($adjustment);
        $this->order->addAdjustment($adjustment);
        $adjustment->setAdjustable($this);
    }

    public function removeAdjustment(AdjustmentInterface $adjustment): void
    {
        if ($adjustment->isLocked() || !$this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
        $this->order->removeAdjustment($adjustment);
        $adjustment->setAdjustable(null);
    }

    public function hasAdjustment(AdjustmentInterface $adjustment): bool
    {
        return $this->adjustments->contains($adjustment);
    }

    public function getAdjustmentsTotal(?string $type = null): int
    {
        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }
    }

    public function recalculateAdjustmentsTotal(): void
    {
        $this->adjustmentsTotal = $this->getAdjustmentsTotal();
    }
}

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

use Sylius\Component\Resource\Model\TimestampableTrait;

class Adjustment implements AdjustmentInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var OrderInterface|null */
    protected $order;

    /** @var OrderItemInterface|null */
    protected $orderItem;

    /** @var OrderItemUnitInterface|null */
    protected $orderItemUnit;

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $label;

    /** @var int */
    protected $amount = 0;

    /** @var bool */
    protected $neutral = false;

    /** @var bool */
    protected $locked = false;

    /** @var string|null */
    protected $originCode;

    /** @var mixed[] */
    protected $details = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __clone()
    {
        $this->id = null;
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAdjustable(): ?AdjustableInterface
    {
        return $this->order ?? $this->orderItem ?? $this->orderItemUnit;
    }

    public function setAdjustable(?AdjustableInterface $adjustable): void
    {
        $this->assertNotLocked();

        $currentAdjustable = $this->getAdjustable();
        if ($currentAdjustable === $adjustable) {
            return;
        }

        $this->order = $this->orderItem = $this->orderItemUnit = null;
        if (null !== $currentAdjustable) {
            $currentAdjustable->removeAdjustment($this);
        }

        if (null === $adjustable) {
            return;
        }

        $this->assignAdjustable($adjustable);
        $adjustable->addAdjustment($this);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
        if (!$this->isNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    public function isNeutral(): bool
    {
        return $this->neutral;
    }

    public function setNeutral(bool $neutral): void
    {
        if ($this->neutral !== $neutral) {
            $this->neutral = $neutral;
            $this->recalculateAdjustable();
        }
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function unlock(): void
    {
        $this->locked = false;
    }

    public function isCharge(): bool
    {
        return 0 > $this->amount;
    }

    public function isCredit(): bool
    {
        return 0 < $this->amount;
    }

    public function getOriginCode(): ?string
    {
        return $this->originCode;
    }

    public function setOriginCode(?string $originCode): void
    {
        $this->originCode = $originCode;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function getOrderItem(): ?OrderItemInterface
    {
        return $this->orderItem;
    }

    public function getOrderItemUnit(): ?OrderItemUnitInterface
    {
        return $this->orderItemUnit;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }

    private function recalculateAdjustable(): void
    {
        $adjustable = $this->getAdjustable();
        if (null !== $adjustable) {
            $adjustable->recalculateAdjustmentsTotal();
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assignAdjustable(AdjustableInterface $adjustable): void
    {
        if ($adjustable instanceof OrderInterface) {
            $this->order = $adjustable;

            return;
        }

        if ($adjustable instanceof OrderItemInterface) {
            $this->orderItem = $adjustable;

            return;
        }

        if ($adjustable instanceof OrderItemUnitInterface) {
            $this->orderItemUnit = $adjustable;

            return;
        }

        throw new \InvalidArgumentException('Given adjustable object class is not supported.');
    }

    /**
     * @throws \LogicException
     */
    private function assertNotLocked(): void
    {
        if ($this->isLocked()) {
            throw new \LogicException('Adjustment is locked and cannot be modified.');
        }
    }
}

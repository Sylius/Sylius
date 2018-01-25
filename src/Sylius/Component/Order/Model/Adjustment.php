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

use Sylius\Component\Resource\Model\TimestampableTrait;

class Adjustment implements AdjustmentInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var OrderInterface|null
     */
    protected $order;

    /**
     * @var OrderItemInterface|null
     */
    protected $orderItem;

    /**
     * @var OrderItemUnitInterface|null
     */
    protected $orderItemUnit;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var bool
     */
    protected $neutral = false;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var string|null
     */
    protected $originCode;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustable(): ?AdjustableInterface
    {
        if (null !== $this->order) {
            return $this->order;
        }

        if (null !== $this->orderItem) {
            return $this->orderItem;
        }

        if (null !== $this->orderItemUnit) {
            return $this->orderItemUnit;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
        if (!$this->isNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isNeutral(): bool
    {
        return $this->neutral;
    }

    /**
     * {@inheritdoc}
     */
    public function setNeutral(bool $neutral): void
    {
        if ($this->neutral !== $neutral) {
            $this->neutral = $neutral;
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function lock(): void
    {
        $this->locked = true;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock(): void
    {
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isCharge(): bool
    {
        return 0 > $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredit(): bool
    {
        return 0 < $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginCode(): ?string
    {
        return $this->originCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginCode(?string $originCode): void
    {
        $this->originCode = $originCode;
    }

    private function recalculateAdjustable(): void
    {
        $adjustable = $this->getAdjustable();
        if (null !== $adjustable) {
            $adjustable->recalculateAdjustmentsTotal();
        }
    }

    /**
     * @param AdjustableInterface $adjustable
     *
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

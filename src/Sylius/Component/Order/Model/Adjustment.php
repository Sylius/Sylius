<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Adjustment implements AdjustmentInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * @var OrderItemUnitInterface
     */
    protected $orderItemUnit;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
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
     * @var string
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
    public function getAdjustable()
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
    public function setAdjustable(AdjustableInterface $adjustable = null)
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setAmount($amount)
    {
        Assert::integer($amount, 'Amount must be an integer.');

        $this->amount = $amount;
        if (!$this->isNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isNeutral()
    {
        return $this->neutral;
    }

    /**
     * {@inheritdoc}
     */
    public function setNeutral($neutral)
    {
        $neutral = (bool) $neutral;

        if ($this->neutral !== $neutral) {
            $this->neutral = $neutral;
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->locked;
    }

    public function lock()
    {
        $this->locked = true;
    }

    public function unlock()
    {
        $this->locked = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isCharge()
    {
        return 0 > $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredit()
    {
        return 0 < $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginCode()
    {
        return $this->originCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginCode($originCode)
    {
        $this->originCode = $originCode;
    }

    private function recalculateAdjustable()
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
    private function assignAdjustable(AdjustableInterface $adjustable)
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
    private function assertNotLocked()
    {
        if ($this->isLocked()) {
            throw new \LogicException('Adjustment is locked and cannot be modified.');
        }
    }
}

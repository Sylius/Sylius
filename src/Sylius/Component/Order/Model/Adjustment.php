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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Adjustment implements AdjustmentInterface
{
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
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * Is adjustment neutral?
     * Should it modify the order total?
     *
     * @var bool
     */
    protected $neutral = false;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var int
     */
    protected $originId;

    /**
     * @var string
     */
    protected $originType;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdjustable(AdjustableInterface $adjustable = null)
    {
        $this->order = $this->orderItem = null;

        if ($adjustable instanceof OrderInterface) {
            $this->order = $adjustable;
        }

        if ($adjustable instanceof OrderItemInterface) {
            $this->orderItem = $adjustable;
        }
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     */
    public function setAmount($amount)
    {
        if (!is_int($amount)) {
            throw new \InvalidArgumentException('Amount must be an integer.');
        }

        $this->amount = $amount;
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
        $this->neutral = (bool) $neutral;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function lock()
    {
        $this->locked = true;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock()
    {
        $this->locked = false;

        return $this;
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
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginType()
    {
        return $this->originType;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginType($originType)
    {
        $this->originType = $originType;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}

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
 * Default adjustment model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Adjustment implements AdjustmentInterface
{
    /**
     * Id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Adjustable order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Adjustable order item.
     *
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * Adjustment label.
     *
     * @var string
     */
    protected $label;

    /**
     * Short description of adjustment.
     *
     * @var string
     */
    protected $description;

    /**
     * Adjustment amount.
     *
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
     * Is adjustment locked?
     *
     * @var bool
     */
    protected $locked = false;

    /**
     * Origin identifier.
     *
     * @var int
     */
    protected $originId;

    /**
     * Origin type.
     *
     * @var string
     */
    protected $originType;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Modification time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
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

        return $this;
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

        return $this;
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

        return $this;
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
        $this->amount = $amount;

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }
}

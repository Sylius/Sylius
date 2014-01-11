<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Model;

/**
 * Default adjustment model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
     * @var integer
     */
    protected $amount;

    /**
     * Is adjustment neutral?
     * Should it modify the order total?
     *
     * @var Boolean
     */
    protected $neutral;

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
        $this->amount = 0;
        $this->neutral = false;
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
        $this->neutral = (Boolean) $neutral;

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

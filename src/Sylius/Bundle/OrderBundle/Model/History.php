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
 * Model for order history
 *
 * @author Myke Hines <myke@webhines.com>
 */
class History implements HistoryInterface
{
    /**
     * Item id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Flag to notify customer
     *
     * @var boolean
     */
    protected $notifyCustomer;

    /**
     * Order Comment
     *
     * @var string
     */
    protected $comment;

    /**
     * State
     *
     * @var integer
     */
    protected $state;

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
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        $this->setState($order->getState());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotifyCustomer()
    {
        return $this->notifyCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotifyCustomer($notifyCustomer = false)
    {
        $this->notifyCustomer = $notifyCustomer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment = '')
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->state = $state;

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

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
 * Order comment.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class Comment implements CommentInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var bool
     */
    protected $notifyCustomer = false;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $author;

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
        if (null !== $order) {
            $this->state = $order->getState();
        }
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
    public function setNotifyCustomer($notifyCustomer)
    {
        $this->notifyCustomer = (bool) $notifyCustomer;
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
    public function setComment($comment = null)
    {
        $this->comment = $comment;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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

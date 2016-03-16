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

/**
 * Order comment.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class Comment implements CommentInterface
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
}

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

use Sylius\Component\Resource\Model\TimestampableInterface;

interface CommentInterface extends TimestampableInterface
{
    /**
     * Return order.
     *
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * Set order.
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);

    /**
     * Return state.
     *
     * @return string
     */
    public function getState();

    /**
     * Set state.
     *
     * @param string $state
     */
    public function setState($state);

    /**
     * Return comment.
     *
     * @return string
     */
    public function getComment();

    /**
     * Set comment.
     *
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * Return notification flag for customer.
     *
     * @return bool
     */
    public function getNotifyCustomer();

    /**
     * Set notification flag for customer.
     *
     * @param bool $notifyCustomer
     */
    public function setNotifyCustomer($notifyCustomer);
}

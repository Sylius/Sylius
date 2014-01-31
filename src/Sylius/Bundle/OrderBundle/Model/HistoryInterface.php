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
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Interface for order line item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface HistoryInterface extends TimestampableInterface
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
     * @return integer
     */
    public function getState();

    /**
     * Set state.
     *
     * @param integer $state
     */
    public function setState($state);

    /**
     * Return comment.
     *
     * @return int
     */
    public function getComment();

    /**
     * Set comment.
     *
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * Return notifyCustomer.
     *
     * @return boolean
     */
    public function getNotifyCustomer();

    /**
     * Set notifyCustomer.
     *
     * @param boolean $notifyCustomer
     */
    public function setNotifyCustomer($notifyCustomer);

}

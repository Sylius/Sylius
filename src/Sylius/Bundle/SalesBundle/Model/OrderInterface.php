<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;

/**
 * Order interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderInterface extends ResourceInterface
{
    /**
     * Get order id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Is confirmed?
     *
     * @return Boolean
     */
    function isConfirmed();

    /**
     * Set confirmed.
     *
     * @param Boolean $confirmed
     */
    function setConfirmed($confirmed);

    /**
     * Generate confirmation token.
     */
    function generateConfirmationToken();

    /**
     * Get confirmation token.
     *
     * @return string
     */
    function getConfirmationToken();

    /**
     * Set confirmation token.
     *
     * @param string $confirmationToken
     */
    function setConfirmationToken($confirmationToken);

    /**
     * Is closed?
     *
     * @return Boolean
     */
    function isClosed();

    /**
     * Set closed.
     *
     * @param Boolean $closed
     */
    function setClosed($closed);

    /**
     * Get order items.
     *
     * @return array An array or collection of OrderItemInterface
     */
    function getItems();

    /**
     * Set items.
     *
     * @param Collection $items
     */
    function setItems(Collection $items);

    /**
     * Returns number of order items.
     *
     * @return integer
     */
    function countItems();

    /**
     * Adds item to order.
     *
     * @param OrderItemInterface $item
     */
    function addItem(OrderItemInterface $item);

    /**
     * Remove item from order.
     *
     * @param OrderItemInterface $item
     */
    function removeItem(OrderItemInterface $item);

    /**
     * Has item in order?
     *
     * @param Item
     */
    function hasItem(OrderItemInterface $item);

    function getTotal();
    function setTotal($total);
    function calculateTotal();

    /**
     * Get creation time.
     *
     * @return \DateTime
     */
    function getCreatedAt();

    /**
     * Set creation time.
     *
     * @param \DateTime $createdAt
     */
    function setCreatedAt(\DateTime $createdAt);

    /**
     * Increments creation time.
     *
     * @return null
     */
    function incrementCreatedAt();

    /**
     * Get modification time.
     *
     * @return \DateTime
     */
    function getUpdatedAt();

    /**
     * Set modification time.
     *
     * @param \DateTime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Increments modification time.
     *
     * @return null
     */
    function incrementUpdatedAt();
}

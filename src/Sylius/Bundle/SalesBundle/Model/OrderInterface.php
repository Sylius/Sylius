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

/**
 * Order interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderInterface
{
    /**
     * Get order id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Set order id.
     *
     * @param mixed $id
     */
    function setId($id);

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
     * Get order status.
     *
     * @return StatusInterface
     */
    function getStatus();

    /**
     * Set order status.
     *
     * @param StatusInterface $status
     */
    function setStatus(StatusInterface $status);

    /**
     * Get order items.
     *
     * @return array An array or collection of ItemInterface
     */
    function getItems();

    /**
     * Set items.
     *
     * @param array $items
     */
    function setItems($items);

    /**
     * Returns number of order items.
     *
     * @return integer
     */
    function countItems();

    /**
     * Adds item to order.
     *
     * @param ItemInterface $item
     */
    function addItem(ItemInterface $item);

    /**
     * Remove item from order.
     *
     * @param ItemInterface $item
     */
    function removeItem(ItemInterface $item);

    /**
     * Has item in order?
     *
     * @param Item
     */
    function hasItem(ItemInterface $item);

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

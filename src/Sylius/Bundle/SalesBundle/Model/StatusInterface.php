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
 * Order status interface.
 * Models representing order status should implement
 * this interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface StatusInterface
{
    /**
     * Get id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Set id.
     *
     * @param mixed $id
     */
    function setId($id);

    /**
     * Get status name.
     *
     * @return string
     */
    function getName();

    /**
     * Set status name.
     * Can be something like 'Waiting for registration'.
     *
     * @param string $name
     */
    function setName($name);

    /**
     * Get status position in list.
     *
     * @return integer
     */
    function getPosition();

    /**
     * Set status position in list.
     *
     * @param integer $position
     */
    function setPosition($position);

    /**
     * Increments status position in list.
     */
    function incrementPosition();

    /**
     * Decrements status position in list.
     */
    function decrementPosition();
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;

/**
 * Address model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface AddressInterface extends ResourceInterface
{
    /**
     * Get creation time.
     *
     * @return DateTime
     */
    function getCreatedAt();

    /**
     * Set creation time.
     *
     * @param DateTime $createdAt
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
     * @param DateTime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Increments modification time.
     *
     * @return null
     */
    function incrementUpdatedAt();
}

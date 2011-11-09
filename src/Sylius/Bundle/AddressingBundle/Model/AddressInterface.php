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

/**
 * Address model interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface AddressInterface
{
    /**
     * Returns address id.
     * 
     * @return integer
     */
    function getId();
    
    /**
     * Get creation time.
     * 
     * @return \DateTime
     */
    function getCreatedAt();
    
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
     * Increments modification time.
     * 
     * @return null
     */
    function incrementUpdatedAt();
}

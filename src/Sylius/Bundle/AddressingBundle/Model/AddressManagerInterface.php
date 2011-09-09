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
 * Address manager interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface AddressManagerInterface
{
    /**
     * Creates address model.
     * 
     * @return AddressInterface
     */
    function createAddress();

    /**
     * Persists address model.
     * 
     * @param AddressInterface $address
     */
    function persistAddress(AddressInterface $address);
    
    /**
     * Removes address model.
     * 
     * @param AddressInterface $address
     */
    function removeAddress(AddressInterface $address);
    
    /**
     * Finds address by id.
     * 
     * @param integer $id The address id
     */
    function findAddress($id);
    
    /**
     * Finds address by criteria.
     * 
     * @param array $criteria The criteria
     */
    function findAddressBy(array $criteria);
    
    /**
     * Finds all adresses.
     * 
     * @return array The adressess
     */
    function findAddresses();
    
    /**
     * Finds addresses by criteria.
     * 
     * @param array $criteria The criteria
     */
    function findAddressesBy(array $criteria);
    
    /**
     * Returns address model class.
     * 
     * @return string The address model class
     */
    function getClass();
    
    /**
     * Sets the address model class.
     * 
     * @param string $class The address model class
     */
    function setClass($class);
}

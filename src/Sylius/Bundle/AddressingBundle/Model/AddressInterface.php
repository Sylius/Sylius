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
     * Returns company.
     * 
     * @return string
     */
    function getCompany();
    
    /**
     * Sets company.
     * 
     * @param string $company
     */
    function setCompany($company);
    
    /**
     * Returns first name.
     * 
     * @return string
     */
    function getName();
    
    /**
     * Sets first name.
     * 
     * @param string $name
     */
    function setName($name);

    /**
     * Returns last name.
     * 
     * @return string
     */
    function getSurname();
    
    /**
     * Sets last name.
     * 
     * @param string $surname
     */
    function setSurname($surname);
    
    /**
     * Returns street.
     * 
     * @return string
     */
    function getStreet();
    
    /**
     * Sets street.
     * 
     * @param string $street
     */
    function setStreet($street);
    
    /**
     * Returns postal code.
     * 
     * @return string
     */
    function getPostcode();
    
    /**
     * Sets postal code.
     * 
     * @param string $postcode
     */
    function setPostcode($postcode);
    
    /**
     * Returns city.
     * 
     * @return string
     */
    function getCity();
    
    /**
     * Sets city.
     * 
     * @param string $city
     */
    function setCity($city);
    
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

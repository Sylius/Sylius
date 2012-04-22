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
 * Common address model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface CommonAddressInterface
{
    /**
     * Get firstname.
     *
     * @return string
     */
    function getFirstname();

    /**
     * Set firstname.
     *
     * @param string $firstname
     */
    function setFirstname($firstname);

    /**
     * Get lastname.
     *
     * @return string
     */
    function getLastname();

    /**
     * Set lastname.
     *
     * @param string $lastname
     */
    function setLastname($lastname);

    /**
     * Get street.
     *
     * @return string
     */
    function getStreet();

    /**
     * Set street.
     *
     * @param string $street
     */
    function setStreet($street);

    /**
     * Get city.
     *
     * @return string
     */
    function getCity();

    /**
     * Set city.
     *
     * @param string $city
     */
    function setCity($city);

    /**
     * Get zip code.
     *
     * @return string
     */
    function getZipCode();

    /**
     * Set zip code.
     *
     * @param string $zipCode
     */
    function setZipCode($zipCode);
}

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
interface AddressInterface
{
    /**
     * Get first name.
     *
     * @return string
     */
    function getFirstName();

    /**
     * Set first name.
     *
     * @param string $firstName
     */
    function setFirstName($firstName);

    /**
     * Get last name.
     *
     * @return string
     */
    function getLastName();

    /**
     * Set last name.
     *
     * @param string $lastName
     */
    function setLastName($lastName);

    /**
     * Get country.
     *
     * @return CountryInterface $country
     */
    function getCountry();

    /**
     * Set country.
     *
     * @param CountryInterface $country
     */
    function setCountry(CountryInterface $country = null);

    /**
     * Get province.
     *
     * @return ProvinceInterface $province
     */
    function getProvince();

    /**
     * Set province.
     *
     * @param ProvinceInterface $province
     */
    function setProvince(ProvinceInterface $province = null);

    /**
     * Is country and province selection valid?
     *
     * @return Boolean
     */
    function isValid();

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
     * Get postcode.
     *
     * @return string
     */
    function getPostcode();

    /**
     * Set postcode.
     *
     * @param string $postcode
     */
    function setPostcode($postcode);

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    function getCreatedAt();

    /**
     * Get modification time.
     *
     * @return \DateTime
     */
    function getUpdatedAt();
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Common address model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface AddressInterface extends TimestampableInterface
{
    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name.
     *
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set last name.
     *
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany();

    /**
     * Set company.
     *
     * @param string $company
     */
    public function setCompany($company);

    /**
     * Get country.
     *
     * @return CountryInterface $country
     */
    public function getCountry();

    /**
     * Set country.
     *
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country = null);

    /**
     * Get province.
     *
     * @return ProvinceInterface $province
     */
    public function getProvince();

    /**
     * Set province.
     *
     * @param ProvinceInterface $province
     */
    public function setProvince(ProvinceInterface $province = null);

    /**
     * Get street.
     *
     * @return string
     */
    public function getStreet();

    /**
     * Set street.
     *
     * @param string $street
     */
    public function setStreet($street);

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity();

    /**
     * Set city.
     *
     * @param string $city
     */
    public function setCity($city);

    /**
     * Get postcode.
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Set postcode.
     *
     * @param string $postcode
     */
    public function setPostcode($postcode);
}

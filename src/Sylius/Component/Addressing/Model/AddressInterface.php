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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AddressInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @return string
     */
    public function getPhoneNumber();

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @param string $company
     */
    public function setCompany($company);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode = null);

    /**
     * @return string
     */
    public function getProvinceCode();

    /**
     * @param string $provinceCode
     */
    public function setProvinceCode($provinceCode = null);

    /**
     * @return string
     */
    public function getProvinceName();

    /**
     * @param string $provinceName
     */
    public function setProvinceName($provinceName = null);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $street
     */
    public function setStreet($street);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode);
}

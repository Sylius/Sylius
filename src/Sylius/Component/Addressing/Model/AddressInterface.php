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
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
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
    public function getPhoneNumber();

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * @return string
     */
    public function getOrganization();

    /**
     * @param string $organization
     */
    public function setOrganization($organization);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     */
    public function setCountry($country = null);

    /**
     * @return string
     */
    public function getAdministrativeArea();

    /**
     * @param string $administrativeAreaCode
     */
    public function setAdministrativeArea($administrativeAreaCode = null);

    /**
     * @return string
     */
    public function getLocality();

    /**
     * @param string $locality
     */
    public function setLocality($locality);

    /**
     * @return string
     */
    public function getDependentLocality();

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality($dependentLocality);

    /**
     * @return string
     */
    public function getFirstAddressLine();

    /**
     * @param string $firstAddressLine
     */
    public function setFirstAddressLine($firstAddressLine);

    /**
     * @return string
     */
    public function getSecondAddressLine();

    /**
     * @param string $secondAddressLine
     */
    public function setSecondAddressLine($secondAddressLine);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode);
}

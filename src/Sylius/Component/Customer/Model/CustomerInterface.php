<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface CustomerInterface extends TimestampableInterface
{
    /**
     * Get email address.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email address.
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name
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
     * Set full name.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender();

    /**
     * Set gender.
     *
     * @param string $gender
     */
    public function setGender($gender);

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * Get addresses.
     *
     * @return Collection|AddressInterface[]
     */
    public function getAddresses();

    /**
     * Add address.
     *
     * @param AddressInterface $address
     */
    public function addAddress(AddressInterface $address);

    /**
     * Remove address.
     *
     * @param AddressInterface $address
     */
    public function removeAddress(AddressInterface $address);

    /**
     * Has address?
     *
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasAddress(AddressInterface $address);
}

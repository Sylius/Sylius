<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

/**
 * Sylius core Order model.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
interface CustomerInterface
{
    /**
     * Set billingAddress
     *
     * @param AddressInterface $billingAddress
     * @return CustomerInterface
     */
    public function setBillingAddress(AddressInterface $billingAddress);

    /**
     * Get billingAddress
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set shippingAddress
     *
     * @param AddressInterface $shippingAddress
     * @return CustomerInterface
     */
    public function setShippingAddress(AddressInterface $shippingAddress);

    /**
     * Get shippingAddress
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Add an address
     *
     * @param AddressInterface $address
     * @return CustomerInterface
     */
    public function addAddress(AddressInterface $address);

    /**
     * Remove an address
     *
     * @param AddressInterface $address
     * @return CustomerInterface
     */
    public function removeAddress(AddressInterface $address);

    /**
     * Has address
     *
     * @param AddressInterface $address
     * @return bool
     */
    public function hasAddress(AddressInterface $address);

    /**
     * Get addresses
     *
     * @return ArrayCollection
     */
    public function getAddresses();

    /**
     * Get orders
     *
     * @return ArrayCollection
     */
    public function getOrders();

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Set user.
     *
     * @param UserInterface $user
     * @return CustomerInterface
     */
    public function setUser(UserInterface $user);

    /**
     * Set first name
     *
     * @param $firstName
     * @return CustomerInterface
     */
    public function setFirstName($firstName);

    /**
     * Get fist name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set last name
     *
     * @param $lastName
     * @return CustomerInterface
     */
    public function setLastName($lastName);

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set currency
     *
     * @param $currency
     * @return CustomerInterface
     */
    public function setCurrency($currency);

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency();
}
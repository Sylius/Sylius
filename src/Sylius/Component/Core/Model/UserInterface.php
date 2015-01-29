<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * User interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface UserInterface extends BaseUserInterface, IdentityInterface, TimestampableInterface
{
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
     * Get orders.
     *
     * @return Collection|OrderInterface[]
     */
    public function getOrders();

    /**
     * Get billing address.
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress = null);

    /**
     * Get shipping address.
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null);

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

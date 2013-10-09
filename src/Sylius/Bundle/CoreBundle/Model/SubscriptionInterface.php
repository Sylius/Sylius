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

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface;

/**
 * SubscriptionInterface
 *
 * Subscription which is linked to a User and Shipping Address
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface extends RecurringSubscriptionInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     * @return SubscriptionInterface
     */
    public function setUser(UserInterface $user);

    /**
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param AddressInterface $address
     * @return SubscriptionInterface
     */
    public function setShippingAddress(AddressInterface $address);
}
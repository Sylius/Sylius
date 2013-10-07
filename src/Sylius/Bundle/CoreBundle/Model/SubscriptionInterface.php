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
use Sylius\Bundle\SubscriptionBundle\Model\LimitedIntervalSubscriptionInterface;

/**
 * SubscriptionInterface
 *
 * Subscription which is linked to a User and Shipping Address
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface extends LimitedIntervalSubscriptionInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();

    public function setUser(UserInterface $user);

    public function getShippingAddress();

    public function setShippingAddress(AddressInterface $address);
}
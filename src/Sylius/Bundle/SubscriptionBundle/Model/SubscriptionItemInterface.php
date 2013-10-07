<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Model;

/**
 * SubscriptionItem Interface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionItemInterface
{
    /**
     * @return Subscription
     */
    public function getSubscription();

    /**
     * @param SubscriptionInterface $subscription
     * @return SubscriptionItemInterface
     */
    public function setSubscription(SubscriptionInterface $subscription = null);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     * @return SubscriptionItemInterface
     */
    public function setQuantity($quantity);
}
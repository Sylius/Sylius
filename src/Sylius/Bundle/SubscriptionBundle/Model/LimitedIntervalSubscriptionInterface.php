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
 * Limited Interval Subscription Interface
 *
 * Implement this interface if you want subscriptions to repeat at a specific interval, optionally with a limit.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface LimitedIntervalSubscriptionInterface
{
    /**
     * Get the number of days that this subscription renews
     *
     * @return int number of days
     */
    public function getInterval();

    /**
     * Set the interval
     *
     * @param int $interval
     * @return LimitedIntervalSubscriptionInterface
     */
    public function setInterval($interval);

    /**
     * Get the number of iterations before this subscription expires
     *
     * @return int|null
     */
    public function getLimit();

    /**
     * Set the limit
     *
     * @param int|null $limit
     * @return LimitedIntervalSubscriptionInterface
     */
    public function setLimit($limit = null);
}
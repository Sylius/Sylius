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
 * Interval Subscription Interface
 *
 * Implemented by subscriptions that repeat at a specific interval
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface RecurringSubscriptionInterface extends SubscriptionInterface, RecurringInterface
{
    /**
     * @param string $unit
     * @return RecurringSubscriptionInterface
     */
    public function setIntervalUnit($unit);

    /**
     * @param int $frequency
     * @return RecurringSubscriptionInterface
     */
    public function setIntervalFrequency($frequency);

    /**
     * @param int $maxCycles
     * @return RecurringSubscriptionInterface
     */
    public function setMaxCycles($maxCycles);
}
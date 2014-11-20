<?php
/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Model;

/**
 * Interval Subscription Interface
 *
 * Implemented by subscriptions that repeat at a specific interval
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface RecurringSubscriptionInterface extends SubscriptionInterface
{
    /**
     * Get unit of interval
     *
     * @return \DateInterval
     */
    public function getInterval();

    /**
     * @param null|\DateInterval $interval
     * @return $this
     */
    public function setInterval(\DateInterval $interval = null);

    /**
     * Get max number of cycles of interval
     *
     * @return int
     */
    public function getMaxCycles();

    /**
     * @param null|int $maxCycles
     * @return $this
     */
    public function setMaxCycles($maxCycles = null);
}

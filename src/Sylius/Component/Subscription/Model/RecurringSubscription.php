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
 * RecurringSubscription implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSubscription extends Subscription implements RecurringSubscriptionInterface
{
    /**
     * @var \DateInterval
     */
    protected $interval;

    /**
     * @var int
     */
    protected $maxCycles;

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval(\DateInterval $interval = null)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxCycles()
    {
        return $this->maxCycles;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxCycles($maxCycles = null)
    {
        $this->maxCycles = $maxCycles;

        return $this;
    }
}

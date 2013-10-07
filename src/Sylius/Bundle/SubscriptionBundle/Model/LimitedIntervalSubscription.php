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
 * LimitedIntervalSubscription implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class LimitedIntervalSubscription extends Subscription implements LimitedIntervalSubscriptionInterface
{
    /**
     * @var int
     */
    protected $interval;

    /**
     * @var int
     */
    protected $limit;

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
    public function setInterval($interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get the number of iterations before this subscription expires
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the limit
     *
     * @param int|null $limit
     * @return LimitedIntervalSubscriptionInterface
     */
    public function setLimit($limit = null)
    {
        $this->limit = $limit;

        return $this;
    }
}
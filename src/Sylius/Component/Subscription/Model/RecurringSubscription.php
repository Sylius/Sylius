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
     * @var string
     */
    protected $intervalUnit;

    /**
     * @var int
     */
    protected $intervalFrequency;

    /**
     * @var int
     */
    protected $maxCycles;

    /**
     * {@inheritdoc}
     */
    public function getIntervalUnit()
    {
        return $this->intervalUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function setIntervalUnit($unit)
    {
        $this->intervalUnit = $unit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervalFrequency()
    {
        return $this->intervalFrequency;
    }

    /**
     * {@inheritdoc}
     */
    public function setIntervalFrequency($frequency)
    {
        $this->intervalFrequency = $frequency;

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

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
 * RecurringInterface
 *
 * Implemented by classes that describe recurrences at specific intervals
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface RecurringInterface
{
    /**
     * Get unit of interval
     *
     * @return string
     */
    public function getIntervalUnit();

    /**
     * Get frequency of interval
     *
     * @return int
     */
    public function getIntervalFrequency();

    /**
     * Get max number of cycles of interval
     *
     * @return int
     */
    public function getMaxCycles();
}

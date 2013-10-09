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
 * SchedulableInterface
 *
 * Implemented by classes that can be scheduled
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SchedulableInterface
{
    /**
     * @return \DateTime
     */
    public function getScheduledDate();

    /**
     * @param \DateTime $date
     */
    public function setScheduledDate(\DateTime $date);
}
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
 * Subscription Interface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface extends SchedulableInterface
{
    /**
     * @return \DateTime
     */
    public function getProcessedDate();

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setProcessedDate(\DateTime $date);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity);
}

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
 * Subscription Interface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface
{
    /**
     * @return \DateTime
     */
    public function getScheduledDate();

    /**
     * @param \DateTime $date
     * @return SubscriptionInterface
     */
    public function setScheduledDate(\DateTime $date);

    /**
     * @return \DateTime
     */
    public function getProcessedDate();

    /**
     * @param \DateTime $date
     * @return SubscriptionInterface
     */
    public function setProcessedDate(\DateTime $date);

    /**
     * @return SubscriptionItemInterface[]
     */
    public function getItems();

    /**
     * @param SubscriptionItemInterface $item
     * @return SubscriptionInterface
     */
    public function addItem(SubscriptionItemInterface $item);

    /**
     * @param SubscriptionItemInterface $item
     * @return SubscriptionInterface
     */
    public function removeItem(SubscriptionItemInterface $item);

    /**
     * Returns number of subscription items.
     *
     * @return integer
     */
    public function countItems();
}
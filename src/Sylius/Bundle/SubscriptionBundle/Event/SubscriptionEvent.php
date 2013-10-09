<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Event;

use Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * SubscriptionEvent
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionEvent extends GenericEvent
{
    public function __construct(SubscriptionInterface $subscription, array $arguments = array())
    {
        parent::__construct($subscription, $arguments);
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->getSubject();
    }
}
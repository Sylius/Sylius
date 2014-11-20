<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Event;

use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Subscription\Model\SubscriptionInterface;

/**
 * SubscriptionEvent
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionEvent extends ResourceEvent
{
    /**
     * @return SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->getSubject();
    }
}

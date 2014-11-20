<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Processor;

/**
 * Processes Subscription entities
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionProcessorInterface
{
    /**
     * Process all Subscriptions scheduled for processing
     */
    public function process();
}

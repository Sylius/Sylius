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

/**
 * Subscription events
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
final class SubscriptionEvents
{
    const SUBSCRIPTION_PROCESS_BATCH_START = 'sylius.subscription_process.batch.start';
    const SUBSCRIPTION_PROCESS_BATCH_END = 'sylius.subscription_process.batch.end';

    const SUBSCRIPTION_PROCESS_INITIALIZE = 'sylius.subscription_process.initialize';
    const SUBSCRIPTION_PROCESS_SUCCESS = 'sylius.subscription_process.success';
    const SUBSCRIPTION_PROCESS_COMPLETED = 'sylius.subscription_process.completed';
    const SUBSCRIPTION_PROCESS_ERROR = 'sylius.subscription_process.error';
}

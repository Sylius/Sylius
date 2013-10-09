<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\EventListener;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * MaxCyclesListener
 *
 * Decrements max cycle and removes subscription when reaching zero
 */
class MaxCyclesListener
{
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function onSubscriptionProcessing(GenericEvent $event)
    {
        $subscription = $event->getSubject();

        if (null === $maxCycles = $subscription->getMaxCycles()) {
            return;
        }

        $subscription->setMaxCycles(max(0, $maxCycles - 1));

        if (0 === $subscription->getMaxCycles()) {
            $this->manager->remove($subscription);
        }
    }
}
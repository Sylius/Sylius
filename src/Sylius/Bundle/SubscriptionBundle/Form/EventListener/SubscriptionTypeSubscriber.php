<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * SubscriptionTypeSubscriber
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionTypeSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        );
    }

    public function onPostSubmit(FormEvent $event)
    {
        if (!$event->getForm()->isValid()) {
            return;
        }

        $subscription = $event->getData();

        foreach ($subscription->getItems() as $item) {
            if (!$item->getQuantity()) {
                $subscription->removeItem($item);
            }
        }
    }
}
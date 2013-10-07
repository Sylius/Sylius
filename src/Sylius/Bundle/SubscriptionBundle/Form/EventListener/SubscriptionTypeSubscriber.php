<?php

namespace Sylius\Bundle\SubscriptionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


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
        $subscription = $event->getData();
        $form = $event->getForm();

        if (!$form->isValid()) {
            return;
        }

        foreach ($subscription->getItems() as $item) {
            if (!$item->getQuantity()) {
                $subscription->removeItem($item);
            }
        }
    }
}
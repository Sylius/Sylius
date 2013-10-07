<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\SubscriptionBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;


class SubscriptionTypeSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Form\EventListener\SubscriptionTypeSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    /**
     * @param Symfony\Component\Form\FormEvent $event
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface $subscription
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $item
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $itemZeroQuantity
     * @param Symfony\Component\Form\Form $form
     */
    function it_removes_zero_quantity_items_from_subscription($event, $subscription, $item, $itemZeroQuantity, $form)
    {
        $item->getQuantity()->willReturn(2);
        $itemZeroQuantity->getQuantity()->willReturn(0);

        $subscription->getItems()->willReturn(array(
            $item,
            $itemZeroQuantity
        ));

        $form->isValid()->willReturn(true);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($subscription);

        $subscription->getItems()->shouldBeCalled();
        $subscription->removeItem($itemZeroQuantity)->shouldBeCalled();

        $this->onPostSubmit($event);
    }

    /**
     * @param Symfony\Component\Form\FormEvent $event
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface $subscription
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $item
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $itemZeroQuantity
     * @param Symfony\Component\Form\Form $form
     */
    function it_does_not_remove_items_when_form_is_invalid($event, $subscription, $item, $itemZeroQuantity, $form)
    {
        $item->getQuantity()->willReturn(2);
        $itemZeroQuantity->getQuantity()->willReturn(0);

        $subscription->getItems()->willReturn(array(
            $item,
            $itemZeroQuantity
        ));

        $form->isValid()->willReturn(false);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($subscription);

        $subscription->getItems()->shouldNotBeCalled();
        $subscription->removeItem($itemZeroQuantity)->shouldNotBeCalled();

        $this->onPostSubmit($event);
    }
}
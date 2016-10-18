<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddDefaultBillingAddressOnOrderFormSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddDefaultBillingAddressOnOrderFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddDefaultBillingAddressOnOrderFormSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_submit_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SUBMIT => 'preSubmit']);
    }

    function it_sets_default_billing_address_before_submitting_form(FormEvent $event)
    {
        $expectedOrderData = [
            'differentBillingAddress' => false,
            'shippingAddress' => [
                'firstName' => 'Jon',
                'lastName' => 'Snow',
            ],
            'billingAddress' => [
                'firstName' => 'Jon',
                'lastName' => 'Snow',
            ],
        ];

        $orderData = [
            'differentBillingAddress' => false,
            'shippingAddress' => [
                'firstName' => 'Jon',
                'lastName' => 'Snow',
            ],
            'billingAddress' => [],
        ];

        $event->getData()->willReturn($orderData);
        $event->setData($expectedOrderData)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_set_default_billing_address_if_different_billing_address_is_requested(FormEvent $event)
    {
        $orderData = [
            'differentBillingAddress' => true,
            'shippingAddress' => [
                'firstName' => 'Jon',
                'lastName' => 'Snow',
            ],
            'billingAddress' => [
                'firstName' => 'Eddard',
                'lastName' => 'Stark',
            ],
        ];
        $event->getData()->willReturn($orderData);
        $event->setData($orderData)->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}

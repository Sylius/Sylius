<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Form\EventSubscriber\AddCustomerGuestTypeFormSubscriber;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @mixin AddCustomerGuestTypeFormSubscriber
 * 
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddCustomerGuestTypeFormSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('customer');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventSubscriber\AddCustomerGuestTypeFormSubscriber');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_set_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_adds_customer_guest_form_type_if_user_is_not_logged_in(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig
    ) {
        $event->getForm()->willReturn($form);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn(null);
        $form->add('customer', 'sylius_customer_guest')->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_sets_customer_on_resource(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig,
        CustomerInterface $customer
    ) {
        $event->getForm()->willReturn($form);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn($customer);
        $form->add('customer', 'sylius_customer_guest')->shouldNotBeCalled();

        $this->preSetData($event);
    }
}

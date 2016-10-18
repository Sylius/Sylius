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
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddCustomerGuestTypeFormSubscriber;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddCustomerGuestTypeFormSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('customer');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddCustomerGuestTypeFormSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_set_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_adds_customer_guest_form_type_if_user_is_not_logged_in_and_resource_does_not_have_customer(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig,
        CustomerAwareInterface $resource
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($resource);
        $resource->getCustomer()->willReturn(null);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn(null);
        $form->add('customer', 'sylius_customer_guest')->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_customer_guest_form_type_if_customer_is_logged_in(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig,
        CustomerInterface $customer,
        CustomerAwareInterface $resource
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($resource);
        $resource->getCustomer()->willReturn(null);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn($customer);
        $form->add('customer', 'sylius_customer_guest')->shouldNotBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_customer_guest_form_type_if_customer_exists_already(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig,
        CustomerAwareInterface $resource,
        CustomerInterface $customer
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($resource);
        $resource->getCustomer()->willReturn($customer);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn(null);
        $form->add('customer', 'sylius_customer_guest')->shouldNotBeCalled();

        $this->preSetData($event);
    }

    function it_throws_invalid_argument_exception_if_resource_is_not_customer_aware(
        FormInterface $form,
        FormEvent $event,
        FormConfigInterface $formConfig,
        ResourceInterface $resource
    ) {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($resource);
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('customer')->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('preSetData', [$event]);
    }
}

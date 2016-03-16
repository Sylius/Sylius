<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class GuestCustomerFormSubscriberSpec extends ObjectBehavior
{
    function let(RepositoryInterface $customerRepository, FactoryInterface $customerFactory, CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerRepository, $customerFactory, $customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventListener\GuestCustomerFormSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_sets_currently_logged_user_as_form_data(
        $customerContext,
        CustomerInterface $customer,
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn(['email' => null]);
        $event->getForm()->willReturn($form);

        $customerContext->getCustomer()->willReturn($customer);

        $form->remove('email')->shouldBeCalled();
        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_new_customer_with_passed_email_as_form_data_if_customer_with_such_email_does_not_exist(
        $customerContext,
        $customerFactory,
        $customerRepository,
        CustomerInterface $customer,
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn(['email' => 'john.doe@example.com']);
        $event->getForm()->willReturn($form);

        $customerContext->getCustomer()->willReturn(null);
        $customerRepository->findOneBy(['email' => 'john.doe@example.com'])->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('john.doe@example.com')->shouldBeCalled();

        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_null_as_form_data_if_no_customer_is_logged_in_and_email_was_not_passed(
        $customerContext,
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn([]);
        $event->getForm()->willReturn($form);

        $customerContext->getCustomer()->willReturn(null);

        $form->setData(null);

        $this->preSubmit($event);
    }

    function it_sets_null_as_form_data_if_customer_with_passed_email_already_exist(
        $customerContext,
        $customerRepository,
        CustomerInterface $customer,
        FormEvent $event,
        FormInterface $form,
        UserInterface $user
    ) {
        $event->getData()->willReturn(['email' => 'john.doe@example.com']);
        $event->getForm()->willReturn($form);

        $customerContext->getCustomer()->willReturn(null);
        $customerRepository->findOneBy(['email' => 'john.doe@example.com'])->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $form->setData(null);

        $this->preSubmit($event);
    }
}

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
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class GuestCustomerFormListenerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $customerRepository, CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerRepository, $customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventListener\GuestCustomerFormListener');
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
        $event->getData()->willReturn(array('email' => null))->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $customerContext->getCustomer()->willReturn($customer)->shouldBeCalled();

        $form->remove('email')->shouldBeCalled();
        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_new_customer_with_passed_email_as_form_data_if_customer_with_such_email_does_not_exist(
        $customerContext,
        $customerRepository,
        CustomerInterface $customer,
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn(array('email' => 'john.doe@example.com'))->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $customerContext->getCustomer()->willReturn(null)->shouldBeCalled();
        $customerRepository->findOneBy(array('email' => 'john.doe@example.com'))->willReturn(null)->shouldBeCalled();

        $customerRepository->createNew()->willReturn($customer)->shouldBeCalled();
        $customer->setEmail('john.doe@example.com')->shouldBeCalled();

        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_null_as_form_data_if_no_customer_is_logged_in_and_email_was_not_passed(
        $customerContext,
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn(array())->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $customerContext->getCustomer()->willReturn(null)->shouldBeCalled();

        $form->setData(null)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_sets_existing_customer_as_form_data_if_customer_with_passed_email_already_exist(
        $customerContext,
        $customerRepository,
        CustomerInterface $customer,
        FormEvent $event,
        FormInterface $form,
        UserInterface $user
    ) {
        $event->getData()->willReturn(array('email' => 'john.doe@example.com'))->shouldBeCalled();
        $event->getForm()->willReturn($form)->shouldBeCalled();

        $customerContext->getCustomer()->willReturn(null)->shouldBeCalled();
        $customerRepository->findOneBy(array('email' => 'john.doe@example.com'))->willReturn($customer)->shouldBeCalled();
        $customer->getUser()->willReturn($user)->shouldBeCalled();

        $form->setData($customer)->shouldBeCalled();

        $this->preSubmit($event);
    }
}

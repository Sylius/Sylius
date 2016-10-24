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
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\CustomerRegistrationFormSubscriber;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerRegistrationFormSubscriberSpec extends ObjectBehavior
{
    function let(RepositoryInterface $customerRepository)
    {
        $this->beConstructedWith($customerRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerRegistrationFormSubscriber::class);
    }

    function it_is_event_subscriber_instance()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_submit_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SUBMIT => 'preSubmit',]);
    }

    function it_sets_user_for_existing_customer(
        FormEvent $event,
        FormInterface $form,
        CustomerInterface $customer,
        RepositoryInterface $customerRepository,
        CustomerInterface $existingCustomer,
        ShopUserInterface $user
    ) {
        $event->getForm()->willReturn($form);
        $form->getData()->willReturn($customer);
        $event->getData()->willReturn(['email' => 'sylius@example.com']);

        $customerRepository->findOneBy(['email' => 'sylius@example.com'])->willReturn($existingCustomer);

        $existingCustomer->getUser()->willReturn(null);
        $customer->getUser()->willReturn($user);

        $existingCustomer->setUser($user)->shouldBeCalled();
        $form->setData($existingCustomer)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_throws_unexpected_type_exception_if_data_is_not_customer_type(
        FormEvent $event,
        FormInterface $form,
        ShopUserInterface $user
    ) {
        $event->getForm()->willReturn($form);
        $form->getData()->willReturn($user);
        $event->getData()->willReturn(['email' => 'sylius@example.com']);

        $this->shouldThrow(UnexpectedTypeException::class)->during('preSubmit', [$event]);
    }

    function it_does_not_set_user_if_customer_with_given_email_has_set_user(
        FormEvent $event,
        FormInterface $form,
        CustomerInterface $customer,
        RepositoryInterface $customerRepository,
        CustomerInterface $existingCustomer,
        ShopUserInterface $user
    ) {
        $event->getForm()->willReturn($form);
        $form->getData()->willReturn($customer);
        $event->getData()->willReturn(['email' => 'sylius@example.com']);

        $customerRepository->findOneBy(['email' => 'sylius@example.com'])->willReturn($existingCustomer);

        $existingCustomer->getUser()->willReturn($user);

        $existingCustomer->setUser($user)->shouldNotBeCalled();
        $form->setData($existingCustomer)->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}

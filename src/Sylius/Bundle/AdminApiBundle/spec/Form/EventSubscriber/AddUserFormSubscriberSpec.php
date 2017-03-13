<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminApiBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AdminApiBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AddUserFormSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('\Fully\Qualified\ClassName');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddUserFormSubscriber::class);
    }

    function it_is_event_subscriber_instance()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_adds_user_form_type_and_create_user_check(
        FormEvent $event,
        Form $form
    ) {
        $event->getForm()->willReturn($form);

        $form->add('user', '\Fully\Qualified\ClassName', Argument::type('array'))->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_removes_user_form_type_by_default(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer
    ) {
        $event->getData()->willReturn([], ['user' => ['plainPassword' => '']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $event->setData([])->shouldBeCalledTimes(1);
        $form->remove('user')->shouldBeCalledTimes(2);

        $this->preSubmit($event);
        $this->preSubmit($event);
    }

    function it_does_not_remove_user_form_type_if_users_data_is_submitted_and_user_data_is_created(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer,
        UserInterface $user
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => 'test']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $form->remove('user')->shouldNotBeCalled();

        $this->preSubmit($event);
    }

    function it_remove_user_form_type_if_users_data_is_not_submitted_and_user_is_not_created(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => '']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $event->setData([])->shouldBeCalled();
        $form->remove('user')->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_remove_user_form_type_if_users_data_is_submitted_and_user_is_not_created(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => 'test']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $form->remove('user')->shouldNotBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_remove_user_form_type_if_users_data_is_not_submitted_and_user_is_created(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer,
        UserInterface $user
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => '']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $form->remove('user')->shouldNotBeCalled();

        $this->preSubmit($event);
    }

    function it_throws_invalid_argument_exception_when_forms_normalized_data_does_not_implement_user_aware_interface(
        FormEvent $event,
        Form $form,
        UserInterface $user
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => '']]);
        $event->getForm()->willReturn($form);
        $form->getNormData()->willReturn($user);

        $this->shouldThrow(\InvalidArgumentException::class)->during('preSubmit', [$event]);
    }
}

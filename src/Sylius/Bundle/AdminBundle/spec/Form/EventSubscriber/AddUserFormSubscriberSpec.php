<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserAwareInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

final class AddUserFormSubscriberSpec extends ObjectBehavior
{
    function it_is_event_subscriber_instance(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_does_not_replace_user_form_by_new_user_form_when_user_has_an_id(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer,
        UserInterface $user,
    ): void {
        $event->getData()->willReturn($customer);
        $event->getForm()->willReturn($form);

        $customer->getUser()->willReturn($user);
        $user->getId()->willReturn(1);
        $user->getPlainPassword()->willReturn(null);

        $customer->setUser(null)->shouldNotBeCalled();
        $event->setData($customer)->shouldNotBeCalled();

        $form->remove('user')->shouldNotBeCalled();
        $form->add('user', '\Fully\Qualified\ClassName', Argument::type('array'))->shouldNotBeCalled();

        $this->submit($event);
    }

    function it_does_not_replace_user_form_by_new_user_form_when_the_password_is_set(
        FormEvent $event,
        Form $form,
        UserAwareInterface $customer,
        UserInterface $user,
    ): void {
        $event->getData()->willReturn($customer);
        $event->getForm()->willReturn($form);

        $customer->getUser()->willReturn($user);
        $user->getId()->willReturn(null);
        $user->getPlainPassword()->willReturn('password');

        $customer->setUser(null)->shouldNotBeCalled();
        $event->setData($customer)->shouldNotBeCalled();

        $form->remove('user')->shouldNotBeCalled();
        $form->add('user', '\Fully\Qualified\ClassName', Argument::type('array'))->shouldNotBeCalled();

        $this->submit($event);
    }

    function it_throws_invalid_argument_exception_when_data_does_not_implement_user_aware_interface(
        FormEvent $event,
        Form $form,
        UserInterface $user,
    ): void {
        $event->getData()->willReturn($user);
        $event->getForm()->willReturn($form);

        $this->shouldThrow(\InvalidArgumentException::class)->during('submit', [$event]);
    }
}

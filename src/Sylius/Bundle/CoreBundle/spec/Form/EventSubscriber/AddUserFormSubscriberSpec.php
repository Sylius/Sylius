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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
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
        $form->add('createUser', Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $this->preSetData($event);
    }
    
    function it_replaces_user_form_by_new_user_form_when_create_user_check_is_not_checked(
        FormEvent $event,
        Form $form,
        Form $createUserCheckForm,
        UserAwareInterface $customer
    ) {
        $event->getData()->willReturn($customer);
        $event->getForm()->willReturn($form);
        $form->get('createUser')->willReturn($createUserCheckForm);
        $createUserCheckForm->getViewData()->willReturn(null);

        $customer->setUser(null)->shouldBeCalled();
        $event->setData($customer)->shouldBeCalled();

        $form->remove('user')->shouldBeCalled();
        $form->add('user', '\Fully\Qualified\ClassName', Argument::type('array'))->shouldBeCalled();

        $this->submit($event);
    }

    function it_does_not_replace_user_form_by_new_user_form_when_create_user_check_is_checked(
        FormEvent $event,
        Form $form,
        Form $createUserCheckForm,
        UserAwareInterface $customer
    ) {
        $event->getData()->willReturn($customer);
        $event->getForm()->willReturn($form);
        $form->get('createUser')->willReturn($createUserCheckForm);
        $createUserCheckForm->getViewData()->willReturn('1');

        $customer->setUser(null)->shouldNotBeCalled();
        $event->setData($customer)->shouldNotBeCalled();

        $form->remove('user')->shouldNotBeCalled();
        $form->add('user', '\Fully\Qualified\ClassName', Argument::type('array'))->shouldNotBeCalled();

        $this->submit($event);
    }

    function it_throws_invalid_argument_exception_when_data_does_not_implement_user_aware_interface(
        FormEvent $event,
        Form $form,
        Form $createUserCheckForm,
        UserInterface $user
    ) {
        $event->getData()->willReturn($user);
        $event->getForm()->willReturn($form);
        $form->get('createUser')->willReturn($createUserCheckForm);
        $createUserCheckForm->getViewData()->willReturn(null);
        
        $this->shouldThrow(\InvalidArgumentException::class)->during('submit', [$event]);
    }
}

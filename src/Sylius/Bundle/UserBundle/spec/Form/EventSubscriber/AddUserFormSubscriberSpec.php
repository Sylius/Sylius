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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AddUserFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventSubscriber\AddUserFormSubscriber');
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

        $form->add('user', 'sylius_user')->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_removes_user_form_type_by_default(
        FormEvent $event,
        Form $form
    ) {
        $event->getData()->willReturn([], ['user' => ['plainPassword' => '']]);
        $event->getForm()->willReturn($form);

        $event->setData([])->shouldBeCalledTimes(1);
        $form->remove('user')->shouldBeCalledTimes(2);

        $this->preSubmit($event);
        $this->preSubmit($event);
    }

    function it_does_not_remove_user_form_type_if_users_data_is_submitted(
        FormEvent $event,
        Form $form
    ) {
        $event->getData()->willReturn(['user' => ['plainPassword' => 'test']]);

        $event->getForm()->shouldNotBeCalled();
        $form->remove('user')->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}

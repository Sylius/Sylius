<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Rbac\Model\RoleInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddParentFormSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('role');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Form\EventSubscriber\AddParentFormSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_add_parent_if_it_is_not_set_and_resource_has_not_id(FormEvent $event, FormInterface $form, RoleInterface $role)
    {
        $event->getData()->willReturn($role);
        $event->getForm()->willReturn($form);

        $role->getId()->willReturn(null);
        $role->getParent()->willReturn(null);

        $form->add('parent', 'sylius_role_choice', Argument::cetera())->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_add_parent_if_it_is_set_and_the_resource_has_id(FormEvent $event, FormInterface $form, RoleInterface $role)
    {
        $event->getData()->willReturn($role);
        $event->getForm()->willReturn($form);

        $role->getId()->willReturn(112);
        $role->getParent()->willReturn(Argument::type(RoleInterface::class));

        $form->add('parent', 'sylius_role_choice', Argument::cetera())->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_add_parent_if_it_is_set_and_the_resource_has_not_id(FormEvent $event, FormInterface $form, RoleInterface $role)
    {
        $event->getData()->willReturn($role);
        $event->getForm()->willReturn($form);

        $role->getId()->willReturn(null);
        $role->getParent()->willReturn(Argument::type(RoleInterface::class));

        $form->add('parent', 'sylius_role_choice', Argument::cetera())->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_parent_if_it_is_not_set_and_the_resource_has_id(FormEvent $event, FormInterface $form, RoleInterface $role)
    {
        $event->getData()->willReturn($role);
        $event->getForm()->willReturn($form);

        $role->getId()->willReturn(112);
        $role->getParent()->willReturn(null);

        $form->add('parent', 'sylius_role_choice', Argument::cetera())->shouldNotBeCalled();

        $this->preSetData($event);
    }

    function it_does_not_add_parent_if_resource_is_not_set(FormEvent $event, FormInterface $form)
    {
        $event->getData()->willReturn(null);
        $form->add('parent', 'sylius_role_choice', Argument::cetera())->shouldNotBeCalled();
        $this->preSetData($event);
    }

    function it_throws_exception_when_resource_does_not_implement_role_interface_or_permission_interface(FormEvent $event, \stdClass $resource)
    {
        $event->getData()->willReturn($resource);
        $this->shouldThrow(UnexpectedTypeException::class)
            ->during('preSetData', [$event]);
    }
}

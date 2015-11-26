<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\EventSubscriber;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddCodeFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(FormEvents::PRE_SET_DATA => 'preSetData'));
    }

    function it_sets_code_as_enabled_when_resource_is_new(FormEvent $event, FormInterface $form, CodeAwareInterface $resource)
    {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->shouldBeCalled()->willReturn('Code12');

        $form
            ->add('code', Argument::type('string'), Argument::containing(true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_sets_code_as_disabled_when_resource_is_not_new(
        FormEvent $event,
        FormInterface $form,
        CodeAwareInterface $resource
    ) {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->shouldBeCalled()->willReturn(null);

        $form
            ->add('code', Argument::type('string'), Argument::containing(false))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_throws_exception_when_resource_does_not_implement_code_aware_interface(FormEvent $event, $object)
    {
        $event->getData()->willReturn($object);
        $this->shouldThrow('\UnexpectedTypeException');
    }
}

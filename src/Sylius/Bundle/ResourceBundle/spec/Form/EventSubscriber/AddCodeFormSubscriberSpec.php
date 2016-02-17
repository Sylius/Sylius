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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
        $this->shouldHaveType(AddCodeFormSubscriber::class);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_sets_code_as_enabled_when_resource_is_new(FormEvent $event, FormInterface $form, CodeAwareInterface $resource)
    {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn(null);

        $form
            ->add('code', Argument::type('string'), Argument::withEntry('disabled', false))
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

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', Argument::type('string'), Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_throws_exception_when_resource_does_not_implement_code_aware_interface(FormEvent $event, $object)
    {
        $event->getData()->willReturn($object);
        $this->shouldThrow('\UnexpectedTypeException');
    }

    function it_sets_code_as_enabled_when_there_is_no_resource(
        FormEvent $event,
        FormInterface $form
    ) {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $form
            ->add('code', 'text', Argument::withEntry('disabled', false))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_specified_type(FormEvent $event, FormInterface $form, CodeAwareInterface $resource)
    {
        $this->beConstructedWith('currency');

        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', 'currency', Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_type_text_by_default(FormEvent $event, FormInterface $form, CodeAwareInterface $resource)
    {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', 'text', Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }
}

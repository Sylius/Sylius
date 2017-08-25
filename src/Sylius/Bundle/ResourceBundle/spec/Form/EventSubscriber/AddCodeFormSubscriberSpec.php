<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddCodeFormSubscriberSpec extends ObjectBehavior
{
    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event(): void
    {
        $this::getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_sets_code_as_enabled_when_resource_is_new(FormEvent $event, FormInterface $form, CodeAwareInterface $resource): void
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
    ): void {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', Argument::type('string'), Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_throws_exception_when_resource_does_not_implement_code_aware_interface(FormEvent $event, $object): void
    {
        $event->getData()->willReturn($object);
        $this->shouldThrow(UnexpectedTypeException::class)->during('preSetData', [$event]);
    }

    function it_sets_code_as_enabled_when_there_is_no_resource(
        FormEvent $event,
        FormInterface $form
    ): void {
        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($form);

        $form
            ->add('code', TextType::class, Argument::withEntry('disabled', false))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_specified_type(FormEvent $event, FormInterface $form, CodeAwareInterface $resource): void
    {
        $this->beConstructedWith(FormType::class);

        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', FormType::class, Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_type_text_by_default(FormEvent $event, FormInterface $form, CodeAwareInterface $resource): void
    {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', TextType::class, Argument::withEntry('disabled', true))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_label_sylius_ui_code_by_default(
        FormEvent $event,
        FormInterface $form,
        CodeAwareInterface $resource
    ): void {
        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('banana_resource');

        $form
            ->add('code', TextType::class, Argument::withEntry('label', 'sylius.ui.code'))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_code_with_specified_type_and_label(
        FormEvent $event,
        FormInterface $form,
        CodeAwareInterface $resource
    ): void {
        $this->beConstructedWith(FormType::class, ['label' => 'sylius.ui.name']);

        $event->getData()->willReturn($resource);
        $event->getForm()->willReturn($form);

        $resource->getCode()->willReturn('Code12');

        $form
            ->add('code', FormType::class, Argument::withEntry('label', 'sylius.ui.name'))
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }
}

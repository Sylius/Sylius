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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\ResourceTranslationsSubscriber;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceTranslationsSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['en_US' => true, 'pl_PL' => false]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceTranslationsSubscriber::class);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this
            ->getSubscribedEvents()
            ->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData', FormEvents::SUBMIT => 'submit'])
        ;
    }

    function it_sets_empty_translations_for_available_locales(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $formConfig
    ) {
        $event->getForm()->willReturn($form);
        $form->getConfig()->willReturn($formConfig);
        $form->has('en_US')->willReturn(false);
        $form->has('pl_PL')->willReturn(false);
        $formConfig->getOption('type')->willReturn('object_translation_type');

        $form->add('en_US', 'object_translation_type', ['required' => true])->shouldBeCalled();
        $form->add('pl_PL', 'object_translation_type', ['required' => false])->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_sets_locale_and_translatable_resource_for_translations(
        FormEvent $event,
        FormInterface $form,
        Collection $data,
        FormInterface $parent,
        TranslatableInterface $translatable,
        TranslationInterface $englishTranslation,
        TranslationInterface $polishTranslation
    ) {
        $event->getData()->willReturn($data);
        $event->getForm()->willReturn($form);
        $form->getParent()->willReturn($parent);
        $parent->getData()->willReturn($translatable);
        $data->getIterator()->willReturn(new \ArrayIterator(['en_US' => $englishTranslation->getWrappedObject(), 'pl_PL' => $polishTranslation->getWrappedObject()]));

        $englishTranslation->setLocale('en_US')->shouldBeCalled();
        $englishTranslation->setTranslatable($translatable)->shouldBeCalled();
        $polishTranslation->setLocale('pl_PL')->shouldBeCalled();
        $polishTranslation->setTranslatable($translatable)->shouldBeCalled();
        $event->setData($data)->shouldBeCalled();

        $this->submit($event);

    }

    function it_removes_empty_translations(
        FormEvent $event,
        FormInterface $form,
        Collection $data,
        FormInterface $parent,
        TranslatableInterface $translatable,
        TranslationInterface $englishTranslation,
        \Iterator $iterator
    ) {
        $event->getData()->willReturn($data);
        $event->getForm()->willReturn($form);
        $form->getParent()->willReturn($parent);
        $parent->getData()->willReturn($translatable);

        $data->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($englishTranslation, null);
        $iterator->key()->willReturn('en_US', 'pl_PL');

        $englishTranslation->setLocale('en_US')->shouldBeCalled();
        $englishTranslation->setTranslatable($translatable)->shouldBeCalled();
        $iterator->next()->shouldBeCalled();
        $data->offsetUnset('pl_PL')->shouldBeCalled();
        $event->setData($data)->shouldBeCalled();

        $this->submit($event);
    }
}

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

namespace spec\Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueCollectionType;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class BuildProductVariantFormSubscriberSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_subscribes_to_event(): void
    {
        static::getSubscribedEvents()->shouldReturn(
            [FormEvents::PRE_SET_DATA => 'preSetData']
        );
    }

    function it_adds_options_on_pre_set_data_event_with_configurable_options(
        FormEvent $event,
        FormFactoryInterface $factory,
        FormInterface $form,
        FormInterface $optionsForm,
        ProductInterface $variable,
        ProductOptionInterface $options,
        ProductOptionValueInterface $optionValue,
        ProductVariantInterface $variant
    ): void {
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($variant);

        $variant->getProduct()->willReturn($variable);
        $variant->getOptionValues()->willReturn(new ArrayCollection([$optionValue->getWrappedObject()]));
        $variable->getOptions()->willReturn(new ArrayCollection([$options->getWrappedObject()]));
        $variable->hasOptions()->willReturn(true);

        $factory->createNamed(
            'optionValues',
            ProductOptionValueCollectionType::class,
            new ArrayCollection([$optionValue->getWrappedObject()]),
            [
                'options' => new ArrayCollection([$options->getWrappedObject()]),
                'auto_initialize' => false,
                'disabled' => false,
            ]
        )->willReturn($optionsForm);

        $form->add($optionsForm)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_options_on_pre_set_data_event_without_configurable_options(
        FormEvent $event,
        FormFactoryInterface $factory,
        FormInterface $form,
        FormInterface $optionsForm,
        ProductInterface $variable,
        ProductOptionInterface $options,
        ProductOptionValueInterface $optionValue,
        ProductVariantInterface $variant
    ): void {
        $this->beConstructedWith($factory, true);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($variant);

        $variant->getProduct()->willReturn($variable);
        $variant->getOptionValues()->willReturn(new ArrayCollection([$optionValue->getWrappedObject()]));
        $variable->getOptions()->willReturn(new ArrayCollection([$options->getWrappedObject()]));
        $variable->hasOptions()->willReturn(true);

        $factory->createNamed(
            'optionValues',
            ProductOptionValueCollectionType::class,
            new ArrayCollection([$optionValue->getWrappedObject()]),
            [
                'options' => new ArrayCollection([$options->getWrappedObject()]),
                'auto_initialize' => false,
                'disabled' => true,
            ]
        )->willReturn($optionsForm);

        $form->add($optionsForm)->shouldBeCalled();

        $this->preSetData($event);
    }
}

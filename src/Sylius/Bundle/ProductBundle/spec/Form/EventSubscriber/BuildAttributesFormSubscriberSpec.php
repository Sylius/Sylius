<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\BuildAttributesFormSubscriber;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class BuildAttributesFormSubscriberSpec extends ObjectBehavior
{
    function let(FactoryInterface $attributeValueFactory, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($attributeValueFactory, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BuildAttributesFormSubscriber::class);
    }

    function it_subscribes_to_event()
    {
        static::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit'
        ]);
    }

    function it_adds_attribute_values_in_different_locales_to_a_product(
        FactoryInterface $attributeValueFactory,
        TranslationLocaleProviderInterface $localeProvider,
        FormEvent $event,
        ProductInterface $product,
        ProductAttributeInterface $attribute,
        ProductAttributeValueInterface $attributeValue,
        ProductAttributeValueInterface $newAttributeValue
    ) {
        $event->getData()->willReturn($product);

        $localeProvider->getDefinedLocalesCodes()->willReturn(['en_US', 'pl_PL']);
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValue->getAttribute()->willReturn($attribute);
        $attributeValue->getLocaleCode()->willReturn('en_US');
        $attributeValue->getCode()->willReturn('mug_material');

        $attributes = new ArrayCollection([$attributeValue->getWrappedObject()]);
        $product->getAttributes()->willReturn($attributes);
        $product->hasAttributeByCodeAndLocale('mug_material', 'en_US')->willReturn(true);
        $product->hasAttributeByCodeAndLocale('mug_material', 'pl_PL')->willReturn(false);

        $attributeValueFactory->createNew()->willReturn($newAttributeValue);
        $newAttributeValue->setAttribute($attribute)->shouldBeCalled();
        $newAttributeValue->setLocaleCode('pl_PL')->shouldBeCalled();
        $product->addAttribute($newAttributeValue)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_removes_empty_attribute_values_in_different_locales(
        FormEvent $event,
        ProductInterface $product,
        ProductAttributeInterface $attribute,
        ProductAttributeValueInterface $attributeValue,
        ProductAttributeValueInterface $attributeValue2
    ) {
        $event->getData()->willReturn($product);

        $attributes = new ArrayCollection([$attributeValue->getWrappedObject(), $attributeValue2->getWrappedObject()]);
        $product->getAttributes()->willReturn($attributes);

        $attribute->getStorageType()->willReturn('text');

        $attributeValue->getValue()->willReturn(null);
        $attributeValue2->getValue()->willReturn('yellow');

        $product->removeAttribute($attributeValue)->shouldBeCalled();

        $this->postSubmit($event);
    }

    function it_throws_an_invalid_argument_exception_if_data_is_not_a_product(FormEvent $event, \stdClass $stdObject)
    {
        $event->getData()->willReturn($stdObject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSetData', [$event])
        ;
    }

    function it_throws_an_invalid_argument_exception_if_data_is_not_a_product_during_submit(FormEvent $event, \stdClass $stdObject)
    {
        $event->getData()->willReturn($stdObject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('postSubmit', [$event])
        ;
    }
}

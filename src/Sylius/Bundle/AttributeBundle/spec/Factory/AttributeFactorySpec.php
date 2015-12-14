<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, LocaleProviderInterface $localeProvider, ServiceRegistryInterface $attributeTypesRegistry)
    {
        $this->beConstructedWith($factory, $localeProvider, $attributeTypesRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Factory\AttributeFactory');
    }

    function it_is_translatable_factory()
    {
        $this->shouldHaveType('Sylius\Component\Translation\Factory\TranslatableFactory');
    }

    function it_creates_new_attribute($attributeTypesRegistry, $factory, $localeProvider, Attribute $attribute, AttributeTypeInterface $attributeType)
    {
        $factory->createNew()->willReturn($attribute);
        $localeProvider->getCurrentLocale()->willReturn('en_US');
        $localeProvider->getFallbackLocale()->willReturn('en_US');

        $attribute->setCurrentLocale('en_US')->shouldBeCalled();
        $attribute->setFallbackLocale('en_US')->shouldBeCalled();

        $attributeType->getStorageType()->willReturn('text');
        $attributeTypesRegistry->get('text')->willReturn($attributeType);

        $attribute->getType()->willReturn('text');
        $attribute->setStorageType('text')->shouldBeCalled();

        $this->createNew()->shouldReturn($attribute);
    }

    function it_creates_typed_attribute($attributeTypesRegistry, $factory, $localeProvider, Attribute $typedAttribute, AttributeTypeInterface $attributeType)
    {
        $factory->createNew()->willReturn($typedAttribute);
        $localeProvider->getCurrentLocale()->willReturn('en_US');
        $localeProvider->getFallbackLocale()->willReturn('en_US');

        $typedAttribute->setCurrentLocale('en_US')->shouldBeCalled();
        $typedAttribute->setFallbackLocale('en_US')->shouldBeCalled();

        $attributeType->getStorageType()->willReturn('datetime');
        $attributeTypesRegistry->get('datetime')->willReturn($attributeType);

        $typedAttribute->setType('datetime')->shouldBeCalled();
        $typedAttribute->getType()->willReturn('datetime');
        $typedAttribute->setStorageType('datetime')->shouldBeCalled();

        $this->createTyped('datetime')->shouldReturn($typedAttribute);
    }
}

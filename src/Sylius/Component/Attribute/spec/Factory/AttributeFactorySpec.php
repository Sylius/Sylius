<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Factory\AttributeFactory;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AttributeFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, ServiceRegistryInterface $attributeTypesRegistry)
    {
        $this->beConstructedWith($factory, $attributeTypesRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeFactory::class);
    }

    function it_implements_attribute_factory_interface()
    {
        $this->shouldImplement(AttributeFactoryInterface::class);
    }

    function it_does_not_allow_to_create_new_attribute()
    {
        $this
            ->shouldThrow(new \BadMethodCallException(
                'Method "createNew()" is not supported for attribute factory. Use "createTyped($type)" instead.'
            ))
            ->during('createNew')
        ;
    }

    function it_creates_typed_attribute(
        Attribute $typedAttribute,
        AttributeTypeInterface $attributeType,
        FactoryInterface $factory,
        ServiceRegistryInterface $attributeTypesRegistry
    ) {
        $factory->createNew()->willReturn($typedAttribute);

        $attributeType->getStorageType()->willReturn('datetime');
        $attributeTypesRegistry->get('datetime')->willReturn($attributeType);

        $typedAttribute->setType('datetime')->shouldBeCalled();
        $typedAttribute->getType()->willReturn('datetime');
        $typedAttribute->setStorageType('datetime')->shouldBeCalled();

        $this->createTyped('datetime')->shouldReturn($typedAttribute);
    }
}

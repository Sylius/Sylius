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

namespace spec\Sylius\Component\Attribute\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Factory\AttributeFactory;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AttributeFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, ServiceRegistryInterface $attributeTypesRegistry): void
    {
        $this->beConstructedWith($factory, $attributeTypesRegistry);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AttributeFactory::class);
    }

    function it_implements_attribute_factory_interface(): void
    {
        $this->shouldImplement(AttributeFactoryInterface::class);
    }

    function it_creates_untyped_attribute(
        FactoryInterface $factory,
        Attribute $untypedAttribute
    ): void {
        $factory->createNew()->willReturn($untypedAttribute);

        $this->createNew()->shouldReturn($untypedAttribute);
    }

    function it_creates_typed_attribute(
        Attribute $typedAttribute,
        AttributeTypeInterface $attributeType,
        FactoryInterface $factory,
        ServiceRegistryInterface $attributeTypesRegistry
    ): void {
        $factory->createNew()->willReturn($typedAttribute);

        $attributeType->getStorageType()->willReturn('datetime');
        $attributeTypesRegistry->get('datetime')->willReturn($attributeType);

        $typedAttribute->setType('datetime')->shouldBeCalled();
        $typedAttribute->getType()->willReturn('datetime');
        $typedAttribute->setStorageType('datetime')->shouldBeCalled();

        $this->createTyped('datetime')->shouldReturn($typedAttribute);
    }
}

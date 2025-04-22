<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Factory\AttributeFactory;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Factory\FactoryInterface;

class AttributeFactoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $factory = $this->createMock(FactoryInterface::class);
        $attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);

        $attributeFactory = new AttributeFactory($factory, $attributeTypesRegistry);

        self::assertInstanceOf(AttributeFactory::class, $attributeFactory);
    }

    public function testShouldImplementsAttributeFactoryInterface(): void
    {
        $factory = $this->createMock(FactoryInterface::class);
        $attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);

        $attributeFactory = new AttributeFactory($factory, $attributeTypesRegistry);

        self::assertInstanceOf(AttributeFactoryInterface::class, $attributeFactory);
    }

    public function testCanCreatesUntypedAttribute(): void
    {
        $factory = $this->createMock(FactoryInterface::class);
        $attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);

        $untypedAttribute = $this->createMock(Attribute::class);
        $factory->expects(self::once())
            ->method('createNew')
            ->willReturn($untypedAttribute);

        $attributeFactory = new AttributeFactory($factory, $attributeTypesRegistry);
        self::assertSame($untypedAttribute, $attributeFactory->createNew());
    }

    /*
    public function testCanCreatesTypedAttribute(): void
    {
        $factory = $this->createMock(FactoryInterface::class);
        $attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);

        $typedAttribute = $this->createMock(Attribute::class);
        $attributeType = $this->createMock(AttributeTypeInterface::class);

        $factory->expects(self::once())
            ->method('createNew')
            ->willReturn($typedAttribute);

        $attributeType->expects(self::once())
            ->method('getStorageType')
            ->willReturn('datetime');

        $attributeTypesRegistry->expects(self::once())
            ->method('get')
            ->with('datetime')
            ->willReturn($attributeType);

        $typedAttribute->expects(self::once())
            ->method('setType')
            ->with('datetime');
        $typedAttribute->expects(self::once())
            ->method('getType')
            ->willReturn('datetime');
        $typedAttribute->expects(self::once())
            ->method('setStorageType')
            ->with('datetime');

        $attributeFactory = new AttributeFactory($factory, $attributeTypesRegistry);

        self::assertSame($typedAttribute, $attributeFactory->createTyped('datetime'));
    }
     */
}

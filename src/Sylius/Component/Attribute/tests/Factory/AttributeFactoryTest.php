<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Factory\AttributeFactory;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Factory\FactoryInterface;

class AttributeFactoryTest extends TestCase
{
    /** @var MockObject&FactoryInterface<Attribute> */
    private FactoryInterface $factory;

    /** @var MockObject&ServiceRegistryInterface */
    private ServiceRegistryInterface $attributeTypesRegistry;

    private AttributeFactory $attributeFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->attributeFactory = new AttributeFactory($this->factory, $this->attributeTypesRegistry);
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(AttributeFactory::class, $this->attributeFactory);
    }

    public function testShouldImplementAttributeFactoryInterface(): void
    {
        self::assertInstanceOf(AttributeFactoryInterface::class, $this->attributeFactory);
    }

    public function testCanCreatesUntypedAttribute(): void
    {
        $untypedAttribute = $this->createMock(Attribute::class);
        $this->factory->expects(self::once())
            ->method('createNew')
            ->willReturn($untypedAttribute);

        self::assertSame($untypedAttribute, $this->attributeFactory->createNew());
    }

    public function testCanCreatesTypedAttribute(): void
    {
        $typedAttribute = $this->createMock(Attribute::class);
        $attributeType = $this->createMock(AttributeTypeInterface::class);

        $this->factory->expects(self::once())
            ->method('createNew')
            ->willReturn($typedAttribute);

        $attributeType->expects(self::once())
            ->method('getStorageType')
            ->willReturn('datetime');

        $this->attributeTypesRegistry->expects(self::once())
            ->method('get')
            ->with('datetime')
            ->willReturn($attributeType);

        $typedAttribute->expects(self::once())
            ->method('setType')
            ->with('datetime');
        $typedAttribute->expects(self::once())
            ->method('setStorageType')
            ->with('datetime');

        self::assertSame($typedAttribute, $this->attributeFactory->createTyped('datetime'));
    }
}

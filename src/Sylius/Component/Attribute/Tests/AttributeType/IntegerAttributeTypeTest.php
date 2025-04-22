<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\AttributeType;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;

class IntegerAttributeTypeTest extends TestCase
{
    private IntegerAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new IntegerAttributeType();
    }
    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(IntegerAttributeType::class, $this->type);
    }

    public function testShouldImplementsAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);
    }

    public function testStorageTypeShouldBeInteger(): void
    {
        self::assertSame('integer', $this->type->getStorageType());
    }

    public function testTypeShouldBeInteger(): void
    {
        self::assertSame('integer', $this->type->getType());
    }
}

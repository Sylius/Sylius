<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\AttributeType;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\FloatAttributeType;

class FloatAttributeTypeTest extends TestCase
{
    private FloatAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new FloatAttributeType();
    }
    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(FloatAttributeType::class, $this->type);
    }

    public function testShouldImplementsAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);
    }

    public function testStorageTypeShouldBeFloat(): void
    {
        self::assertSame('float', $this->type->getStorageType());
    }

    public function testTypeShouldBeFloat(): void
    {
        self::assertSame('float', $this->type->getType());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\AttributeType;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;

class CheckboxAttributeTypeTest extends TestCase
{
    private CheckboxAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new CheckboxAttributeType();
    }
    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(CheckboxAttributeType::class, $this->type);
    }

    public function testShouldImplementsAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);;
    }

    public function testStorageShouldBeBoolean(): void
    {
        self::assertSame('boolean', $this->type->getStorageType());
    }

    public function testTypeShouldBeCheckbox(): void
    {
        self::assertSame('checkbox', $this->type->getType());
    }
}

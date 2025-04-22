<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\AttributeType;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;

class DateAttributeTypeTest extends TestCase
{
    private DateAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new DateAttributeType();
    }
    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(DateAttributeType::class, $this->type);
    }

    public function testShouldImplementsAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);
    }

    public function testStorageTypeShouldBeDate(): void
    {
        self::assertSame('date', $this->type->getStorageType());
    }

    public function testTypeShouldBeDate(): void
    {
        self::assertSame('date', $this->type->getType());
    }
}

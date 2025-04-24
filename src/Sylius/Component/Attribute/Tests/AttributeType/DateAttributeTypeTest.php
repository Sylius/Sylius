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

    public function testShouldImplementAttributeTypeInterface(): void
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

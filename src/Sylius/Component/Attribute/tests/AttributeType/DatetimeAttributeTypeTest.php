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
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;

class DatetimeAttributeTypeTest extends TestCase
{
    private DatetimeAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new DatetimeAttributeType();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(DatetimeAttributeType::class, $this->type);
    }

    public function testShouldImplementAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);
    }

    public function testStorageTypeShouldBeDatetime(): void
    {
        self::assertSame('datetime', $this->type->getStorageType());
    }

    public function testTypeShouldBeDateTime(): void
    {
        self::assertSame('datetime', $this->type->getType());
    }
}

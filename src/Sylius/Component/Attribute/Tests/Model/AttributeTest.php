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

namespace Tests\Sylius\Component\Attribute\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;

class AttributeTest extends TestCase
{
    private Attribute $attribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attribute = new Attribute();
        $this->attribute->setCurrentLocale('en_US');
        $this->attribute->setFallbackLocale('en_US');
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(Attribute::class, $this->attribute);
    }

    public function testShouldImplementAttributeInterface(): void
    {
        self::assertInstanceOf(AttributeInterface::class, $this->attribute);
    }

    public function testShouldHasNoIdByDefault(): void
    {
        self::assertNull($this->attribute->getId());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->attribute->setCode('t_shirt_collection');
        self::assertSame('t_shirt_collection', $this->attribute->getCode());
    }

    public function testShouldHasNoNameByDefault(): void
    {
        self::assertNull($this->attribute->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->attribute->setName('T-Shirt collection');
        self::assertSame('T-Shirt collection', $this->attribute->getName());
    }

    public function testShouldReturnsNameWhenConvertedToString(): void
    {
        $this->attribute->setName('T-Shirt material');
        self::assertSame('T-Shirt material', (string) $this->attribute);
    }

    public function testShouldHasTextTypeByDefault(): void
    {
        self::assertSame(TextAttributeType::TYPE, $this->attribute->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        $this->attribute->setType(CheckboxAttributeType::TYPE);
        self::assertSame(CheckboxAttributeType::TYPE, $this->attribute->getType());
    }

    public function testShouldInitializesAnEmptyConfigurationArrayByDefault(): void
    {
        self::assertSame([], $this->attribute->getConfiguration());
    }

    public function testConfigurationShouldBeMutable(): void
    {
        $this->attribute->setConfiguration(['format' => 'd/m/Y']);
        self::assertSame(['format' => 'd/m/Y'], $this->attribute->getConfiguration());
    }

    public function testStorageTypeShouldBeMutable(): void
    {
        $this->attribute->setStorageType('text');
        self::assertSame('text', $this->attribute->getStorageType());
    }

    public function testShouldInitializesCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->attribute->getCreatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->attribute->setCreatedAt($date);
        self::assertSame($date, $this->attribute->getCreatedAt());
    }

    public function testShouldHasNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->attribute->getUpdatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->attribute->setUpdatedAt($date);
        self::assertSame($date, $this->attribute->getUpdatedAt());
    }

    public function testShouldHasNoPosition(): void
    {
        self::assertNull($this->attribute->getPosition());
        $this->attribute->setPosition(0);
        self::assertSame(0, $this->attribute->getPosition());
    }
}

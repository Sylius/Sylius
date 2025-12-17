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
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeValue;
use Sylius\Component\Attribute\Model\AttributeValueInterface;

class AttributeValueTest extends TestCase
{
    private AttributeValue $attributeValue;

    protected function setUp(): void
    {
        $this->attributeValue = new AttributeValue();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(AttributeValue::class, $this->attributeValue);
    }

    public function testShouldImplementAttributeValueInterface(): void
    {
        self::assertInstanceOf(AttributeValueInterface::class, $this->attributeValue);
    }

    public function testShouldHasNoIdByDefault(): void
    {
        self::assertNull($this->attributeValue->getId());
    }

    public function testDoesNotBelongToASubjectByDefault(): void
    {
        self::assertNull($this->attributeValue->getSubject());
    }

    public function testShouldAllowAssigningItselfToASubject(): void
    {
        $subject = $this->createMock(AttributeSubjectInterface::class);
        $this->attributeValue->setSubject($subject);
        self::assertSame($subject, $this->attributeValue->getSubject());
    }

    public function testShouldHasNoAttributeDefinedByDefault(): void
    {
        self::assertNull($this->attributeValue->getAttribute());
    }

    public function testShouldAllowAttributeToBeDefined(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $this->attributeValue->setAttribute($attribute);
        self::assertSame($attribute, $this->attributeValue->getAttribute());
    }

    public function testShouldHasNoLocaleByDefault(): void
    {
        self::assertNull($this->attributeValue->getLocaleCode());
    }

    public function testLocaleShouldBeMutable(): void
    {
        $this->attributeValue->setLocaleCode('en');
        self::assertSame('en', $this->attributeValue->getLocaleCode());
    }

    public function testShouldHasNoValueByDefault(): void
    {
        self::assertNull($this->attributeValue->getValue());
    }

    public function testValueShouldBeMutableBasedOnAttributeStorageType(): void
    {
        $storageTypeToExampleData = [
            'boolean' => false,
            'text' => 'Lorem ipsum',
            'integer' => 42,
            'float' => 6.66,
            'datetime' => new \DateTime(),
            'date' => new \DateTime(),
            'json' => ['foo' => 'bar'],
        ];

        foreach ($storageTypeToExampleData as $storageType => $exampleData) {
            $attribute = $this->createMock(AttributeInterface::class);
            $attribute
                ->method('getStorageType')
                ->willReturn($storageType);
            $this->attributeValue->setAttribute($attribute);

            $this->attributeValue->setValue($exampleData);
            self::assertSame($exampleData, $this->attributeValue->getValue());
        }
    }

    public function testValueCanBeSetToNull(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);

        $storageTypes = [
            'boolean',
            'text',
            'integer',
            'float',
            'datetime',
            'date',
            'json',
        ];

        foreach ($storageTypes as $storageType) {
            $attribute
                ->method('getStorageType')
                ->willReturn($storageType);
            $this->attributeValue->setAttribute($attribute);

            $this->attributeValue->setValue(null);
            self::assertNull($this->attributeValue->getValue());
        }
    }

    public function testShouldThrowExceptionWhenTryingToGetCodeWithoutAttributeDefined(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->attributeValue->getCode();
    }

    public function testShouldReturnsAttributeCode(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->method('getCode')
            ->willReturn('tshirt_material');
        $this->attributeValue->setAttribute($attribute);

        self::assertSame('tshirt_material', $this->attributeValue->getCode());
    }

    public function testShouldThrowsExceptionWhenTryingToGetNameWithoutAttributeDefined(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->attributeValue->getName();
    }

    public function testShouldReturnsAttributeName(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->method('getName')
            ->willReturn('T-Shirt material');
        $this->attributeValue->setAttribute($attribute);

        self::assertSame('T-Shirt material', $this->attributeValue->getName());
    }

    public function testShouldThrowsExceptionWhenTryingToGetTypeWithoutAttributeDefined(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->attributeValue->getType();
    }

    public function testShouldReturnsAttributeType(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute
            ->method('getType')
            ->willReturn('choice');
        $this->attributeValue->setAttribute($attribute);

        self::assertSame('choice', $this->attributeValue->getType());
    }
}

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
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class TextAttributeTypeTest extends TestCase
{
    private TextAttributeType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new TextAttributeType();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(TextAttributeType::class, $this->type);
    }

    public function testShouldImplementAttributeTypeInterface(): void
    {
        self::assertInstanceOf(AttributeTypeInterface::class, $this->type);
    }

    public function testStorageTypeShouldBeText(): void
    {
        self::assertSame('text', $this->type->getStorageType());
    }

    public function testTypeShouldBeText(): void
    {
        self::assertSame('text', $this->type->getType());
    }

    public function testChecksIfAttributeValueIsValidAccordingToMinAndMaxConstraint(): void
    {
        $attributeValue = $this->createMock(AttributeValueInterface::class);
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolationList = $this->createMock(ConstraintViolationListInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $attributeValue->expects(self::once())->method('getValue')->willReturn('X');

        $context->expects(self::once())->method('getValidator')->willReturn($validator);

        $validator->expects(self::once())
            ->method('validate')
            ->with('X', self::callback(function ($constraints) {
                foreach ($constraints as $constraint) {
                    if ($constraint instanceof Length) {
                        return true;
                    }
                }

                return false;
            }))
            ->willReturn($constraintViolationList);

        $constraintViolationList->expects(self::once())->method('rewind');

        $constraintViolationList->expects(self::exactly(2))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);

        $constraintViolationList->expects(self::once())
            ->method('current')
            ->willReturn($constraintViolation);

        $constraintViolationList->expects(self::once())->method('next');

        $constraintViolation->expects(self::once())
            ->method('getMessage')
            ->willReturn('error message');

        $context->expects(self::once())
            ->method('buildViolation')
            ->with('error message')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())
            ->method('atPath')
            ->with('value')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        $this->type->validate($attributeValue, $context, ['min' => 2, 'max' => 255]);
    }

    public function testChecksIfAttributeValueIsValidAccordingToRequiredConstraint(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attributeValue = $this->createMock(AttributeValueInterface::class);
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolationList = $this->createMock(ConstraintViolationListInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $attributeValue->expects(self::once())->method('getValue')->willReturn(null);

        $context->expects(self::once())->method('getValidator')->willReturn($validator);

        $validator->expects(self::once())
            ->method('validate')
            ->with(null, self::callback(function ($constraints) {
                foreach ($constraints as $constraint) {
                    if ($constraint instanceof NotBlank) {
                        return true;
                    }
                }

                return false;
            }))
            ->willReturn($constraintViolationList);

        $constraintViolationList->expects(self::once())->method('rewind');
        $constraintViolationList->expects(self::exactly(2))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);
        $constraintViolationList->expects(self::once())->method('current')->willReturn($constraintViolation);
        $constraintViolationList->expects(self::once())->method('next');
        $constraintViolation->expects(self::once())->method('getMessage')->willReturn('error message');
        $context->expects(self::once())
            ->method('buildViolation')
            ->with('error message')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects(self::once())
            ->method('atPath')
            ->with('value')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        $this->type->validate($attributeValue, $context, ['required' => true]);
    }
}

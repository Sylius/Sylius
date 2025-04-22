<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Attribute\AttributeType;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\AttributeType\PercentAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PercentAttributeTypeTest extends TestCase
{
    private PercentAttributeType $type;
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new PercentAttributeType();
    }
    public function testCanBeInstantiatednstantiated(): void
    {
        self::assertInstanceOf(PercentAttributeType::class, $this->type);
    }
    public function testStorageTypeShouldBeFloat(): void
    {
        self::assertSame('float', $this->type->getStorageType());
    }

    public function testTypeShouldBePercent(): void
    {
        self::assertSame('percent', $this->type->getType());
    }

    /*
    public function testChecksIfAttributeValueIsValid(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attributeValue = $this->createMock(AttributeValueInterface::class);
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolation = $this->createMock(ConstraintViolationInterface::class);
        $constraintViolationList = $this->createMock(ConstraintViolationListInterface::class);
        $context = $this->createMock(ExecutionContextInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        // AttributeValue will return Attribute
        $attributeValue->expects(self::once())
            ->method('getAttribute')
            ->willReturn($attribute);

        // AttributeValue value is null
        $attributeValue->expects(self::once())
            ->method('getValue')
            ->willReturn(null);

        // Context should return the Validator
        $context->expects(self::once())
            ->method('getValidator')
            ->willReturn($validator);

        // Validator called, returns violations list
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

        // Violation iteration: valid() yields true (once), then false; current() yields violation
        $constraintViolationList->expects(self::once())
            ->method('rewind');
        $constraintViolationList->expects(self::exactly(2))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);
        $constraintViolationList->expects(self::once())
            ->method('current')
            ->willReturn($constraintViolation);
        $constraintViolationList->expects(self::once())
            ->method('next');

        // Violation's message
        $constraintViolation->expects(self::once())
            ->method('getMessage')
            ->willReturn('error message');

        // Context builds violation
        $context->expects(self::once())
            ->method('buildViolation')
            ->with('error message')
            ->willReturn($constraintViolationBuilder);

        // The builder's atPath('value') and addViolation() are chained/called
        $constraintViolationBuilder->expects(self::once())
            ->method('atPath')
            ->with('value')
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->expects(self::once())
            ->method('addViolation');

        $type = new PercentAttributeType();

        // Run validation (should not throw)
        $type->validate($attributeValue, $context, ['required' => true]);
    }
    */
}

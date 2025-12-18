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

namespace Tests\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfiguration;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfigurationValidator;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfiguration;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ValidSelectAttributeConfigurationValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private ValidSelectAttributeConfigurationValidator $validator;

    private AttributeInterface&MockObject $attributeMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ValidSelectAttributeConfigurationValidator();
        $this->attributeMock = $this->createMock(AttributeInterface::class);
        $this->initialize($this->context);
    }

    private function initialize(ExecutionContextInterface $context): void
    {
        $this->validator->initialize($context);
    }

    public function testAddsAViolationIfMaxEntriesValueIsLowerThanMinEntriesValue(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(SelectAttributeType::TYPE);

        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn(['multiple' => true, 'min' => 6, 'max' => 4]);

        $this->context->expects(self::once())->method('addViolation');

        $this->validator->validate($this->attributeMock, $constraint);
    }

    public function testAddsAViolationIfMinEntriesValueIsGreaterThanTheNumberOfAddedChoices(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(SelectAttributeType::TYPE);

        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn([
            'multiple' => true,
            'min' => 4,
            'max' => 6,
            'choices' => [
                'ec134e10-6a80-4eaf-8346-e9bb0f7406a4' => 'Banana',
                '63148775-be39-47eb-8afd-a4818981e3c0' => 'Watermelon',
            ],
        ]);

        $this->context->expects(self::once())->method('addViolation');

        $this->validator->validate($this->attributeMock, $constraint);
    }

    public function testAddsAViolationIfMultipleIsNotTrueWhenMinOrMaxEntriesValuesAreSpecified(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(SelectAttributeType::TYPE);

        $this->attributeMock->expects(self::atLeastOnce())
            ->method('getConfiguration')
            ->willReturn([
                'multiple' => false,
                'min' => 4,
                'max' => 6,
            ]);

        $this->context->expects(self::once())->method('addViolation');

        $this->validator->validate($this->attributeMock, $constraint);
    }

    public function testAddsAViolationIfMultipleIsNotSetWhenMinOrMaxEntriesValuesAreSpecified(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(SelectAttributeType::TYPE);

        $this->attributeMock->expects(self::atLeastOnce())
            ->method('getConfiguration')
            ->willReturn([
                'min' => 4,
                'max' => 6,
            ]);

        $this->context->expects(self::once())->method('addViolation');

        $this->validator->validate($this->attributeMock, $constraint);
    }

    public function testDoesNothingIfAnAttributeIsNotASelectType(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(TextAttributeType::TYPE);

        $this->context->expects(self::never())->method('addViolation');

        $this->validator->validate($this->attributeMock, $constraint);
    }

    public function testThrowsAnExceptionIfValidatedValueIsNotAnAttribute(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate('badObject', $constraint);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAValidSelectAttributeConfigurationConstraint(): void
    {
        $constraint = new ValidTextAttributeConfiguration();
        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate($this->attributeMock, $constraint);
    }
}

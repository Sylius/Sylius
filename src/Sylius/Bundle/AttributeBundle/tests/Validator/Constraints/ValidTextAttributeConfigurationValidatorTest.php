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
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfiguration;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfigurationValidator;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ValidTextAttributeConfigurationValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private ValidTextAttributeConfigurationValidator $validTextAttributeConfigurationValidator;

    private AttributeInterface&MockObject $attributeMock;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validTextAttributeConfigurationValidator = new ValidTextAttributeConfigurationValidator();
        $this->initialize($this->context);
        $this->attributeMock = $this->createMock(AttributeInterface::class);
    }

    private function initialize(ExecutionContextInterface $context): void
    {
        $this->validTextAttributeConfigurationValidator->initialize($context);
    }

    public function testAddsAViolationIfMaxEntriesValueIsLowerThanMinEntriesValue(): void
    {
        $constraint = new ValidTextAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(TextAttributeType::TYPE);

        $this->attributeMock->expects(self::once())
            ->method('getConfiguration')
            ->willReturn([
                'min' => 6,
                'max' => 4,
            ]);

        $this->context->expects(self::once())->method('addViolation');

        $this->validTextAttributeConfigurationValidator->validate($this->attributeMock, $constraint);
    }

    public function testDoesNothingIfAnAttributeIsNotATextType(): void
    {
        $constraint = new ValidTextAttributeConfiguration();
        $this->attributeMock->expects(self::once())
            ->method('getType')
            ->willReturn(SelectAttributeType::TYPE);

        $this->context->expects(self::never())->method('addViolation');

        $this->validTextAttributeConfigurationValidator->validate($this->attributeMock, $constraint);
    }

    public function testThrowsAnExceptionIfValidatedValueIsNotAnAttribute(): void
    {
        $constraint = new ValidTextAttributeConfiguration();
        self::expectException(\InvalidArgumentException::class);

        $this->validTextAttributeConfigurationValidator->validate('badObject', $constraint);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAValidTextAttributeConfigurationConstraint(): void
    {
        $constraint = new ValidSelectAttributeConfiguration();
        self::expectException(\InvalidArgumentException::class);

        $this->validTextAttributeConfigurationValidator->validate($this->attributeMock, $constraint);
    }
}

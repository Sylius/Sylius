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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\MaxInteger;
use Sylius\Bundle\CoreBundle\Validator\Constraints\MaxIntegerValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class MaxIntegerValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private MaxIntegerValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new MaxIntegerValidator(100);
        $this->validator->initialize($this->context);
    }

    public function testItDoesNotValidateValueLessThanMax(): void
    {
        $this->context
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate(50, new MaxInteger());
    }

    public function testItValidatesValueEqualToMax(): void
    {
        $constraint = new MaxInteger();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ compared_value }}', '100')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate(100, $constraint);
    }

    public function testItValidatesValueGreaterThanMax(): void
    {
        $constraint = new MaxInteger();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ compared_value }}', '100')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate(150, $constraint);
    }
}

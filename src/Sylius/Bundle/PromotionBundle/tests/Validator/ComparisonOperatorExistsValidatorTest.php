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

namespace Tests\Sylius\Bundle\PromotionBundle\Validator;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Validator\ComparisonOperatorExistsValidator;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\ComparisonOperatorExists;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[AllowMockObjectsWithoutExpectations]
final class ComparisonOperatorExistsValidatorTest extends TestCase
{
    private ComparisonOperatorMatcherInterface&MockObject $comparisonOperatorMatcher;

    private ExecutionContextInterface&MockObject $context;

    private ComparisonOperatorExistsValidator $validator;

    protected function setUp(): void
    {
        $this->comparisonOperatorMatcher = $this->createMock(ComparisonOperatorMatcherInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new ComparisonOperatorExistsValidator($this->comparisonOperatorMatcher);
        $this->validator->initialize($this->context);
    }

    public function testThrowsAnExceptionIfConstraintIsNotComparisonOperatorExists(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('>=', $this->createMock(Constraint::class));
    }

    public function testDoesNothingIfValueIsEmpty(): void
    {
        $this->comparisonOperatorMatcher->expects($this->never())->method('getAvailableComparisonOperators');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, new ComparisonOperatorExists());
    }

    public function testDoesNothingIfComparisonOperatorExists(): void
    {
        $this->comparisonOperatorMatcher
            ->expects($this->once())
            ->method('getAvailableComparisonOperators')
            ->willReturn(['greater_than_equal' => '>='])
        ;
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('>=', new ComparisonOperatorExists());
    }

    public function testAddsViolationIfComparisonOperatorDoesNotExist(): void
    {
        $constraint = new ComparisonOperatorExists();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->comparisonOperatorMatcher
            ->expects($this->once())
            ->method('getAvailableComparisonOperators')
            ->willReturn([
                'greater_than_equal' => '>=',
                'lower_than' => '<',
            ])
        ;
        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;
        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ available_comparison_operators }}', '>=, <')
            ->willReturn($violationBuilder)
        ;
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('invalid', $constraint);
    }
}

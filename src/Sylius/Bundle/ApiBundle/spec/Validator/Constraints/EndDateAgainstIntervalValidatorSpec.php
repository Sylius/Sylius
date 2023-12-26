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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Validator\Constraints\EndDateAgainstInterval;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class EndDateAgainstIntervalValidatorSpec extends ObjectBehavior
{
    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_date_period(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new EndDateAgainstInterval()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_end_date_against_interval(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), new \DateTimeImmutable()),
                new class() extends Constraint {
                },
            ]);
    }

    function it_adds_violation_if_end_date_is_not_a_multiple_of_interval(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $this->initialize($executionContext);
        $constraint = new EndDateAgainstInterval();

        $constraintViolationBuilder
            ->setParameter('%expectedDate%', '2019-01-02 23:59:59')
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder
            ->setParameter('%givenDate%', '2019-01-02 23:59:58')
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $executionContext->buildViolation($constraint->message)->willReturn($constraintViolationBuilder);

        $this->validate(
            new \DatePeriod(
                new \DateTimeImmutable('2019-01-01 00:00:00'),
                new \DateInterval('P1D'),
                new \DateTimeImmutable('2019-01-02 23:59:58'),
            ),
            $constraint,
        );
    }

    function it_does_not_add_violation_if_end_date_is_a_multiple_of_interval(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);
        $constraint = new EndDateAgainstInterval();

        $executionContext->buildViolation($constraint->message)->shouldNotBeCalled();

        $this->validate(
            new \DatePeriod(
                new \DateTimeImmutable('2019-01-01 00:00:00'),
                new \DateInterval('P1D'),
                new \DateTimeImmutable('2019-01-03 23:59:59'),
            ),
            $constraint,
        );
    }
}

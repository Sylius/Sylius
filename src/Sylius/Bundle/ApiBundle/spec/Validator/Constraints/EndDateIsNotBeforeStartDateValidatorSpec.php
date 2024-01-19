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
use Sylius\Bundle\ApiBundle\Validator\Constraints\EndDateIsNotBeforeStartDate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class EndDateIsNotBeforeStartDateValidatorSpec extends ObjectBehavior
{
    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_date_period(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new EndDateIsNotBeforeStartDate()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_end_date_is_not_before_start_date(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), new \DateTimeImmutable()),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_end_date_is_before_start_date(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);
        $constraint = new EndDateIsNotBeforeStartDate();

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate(
            new \DatePeriod(
                new \DateTimeImmutable('2020-01-01'),
                new \DateInterval('P1D'),
                new \DateTimeImmutable('2019-01-01'),
            ),
            $constraint,
        );
    }

    function it_does_not_add_violation_if_end_date_is_after_start_date(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);
        $constraint = new EndDateIsNotBeforeStartDate();

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate(
            new \DatePeriod(
                new \DateTimeImmutable('2019-01-01'),
                new \DateInterval('P1D'),
                new \DateTimeImmutable('2020-01-01'),
            ),
            $constraint,
        );
    }
}

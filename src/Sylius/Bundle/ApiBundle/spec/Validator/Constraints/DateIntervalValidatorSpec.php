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
use Sylius\Bundle\ApiBundle\Validator\Constraints\DateInterval;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class DateIntervalValidatorSpec extends ObjectBehavior
{
    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_date_interval(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new DateInterval()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_date_interval(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new \DateInterval('P1D'),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_date_interval_format_is_invalid(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
        $constraint = new DateInterval();

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate('INVALID_DATE_INTERVAL_FORMAT', $constraint);
    }

    function it_does_not_add_violation_if_date_interval_format_is_valid(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);
        $constraint = new DateInterval();

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate('P1D', $constraint);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\ResetPassword;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPassword;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ConfirmResetPasswordValidatorSpec extends ObjectBehavior
{
    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_does_not_add_violation_if_passwords_are_same(ExecutionContextInterface $executionContext): void
    {
        $constraint = new ConfirmResetPassword();
        $this->initialize($executionContext);

        $value = new ResetPassword('token');
        $value->newPassword = 'password';
        $value->confirmNewPassword = 'password';

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_passwords_are_different(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $constraint = new ConfirmResetPassword();
        $constraint->message = 'message';
        $this->initialize($executionContext);

        $value = new ResetPassword('token');
        $value->newPassword = 'password';
        $value->confirmNewPassword = 'notaPassword';

        $executionContext->buildViolation($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('newPassword')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }
}

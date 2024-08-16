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
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpired;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AdminResetPasswordTokenNonExpiredValidatorSpec extends ObjectBehavior
{
    public function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository, 'P5D');
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    public function it_throws_exception_when_value_is_not_a_reset_password(): void
    {
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', $constraint])
        ;
    }

    public function it_throws_exception_when_constraint_is_not_admin_reset_password_token_non_expired(
        Constraint $constraint,
    ): void {
        $value = new ResetPassword('token');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    public function it_does_nothing_when_a_user_for_given_token_does_not_exist(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $value = new ResetPassword('token');
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this->initialize($executionContext);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn(null);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    public function it_does_nothing_when_user_password_reset_token_is_non_expired(
        UserRepositoryInterface $userRepository,
        AdminUserInterface $adminUser,
        ExecutionContextInterface $executionContext,
    ): void {
        $value = new ResetPassword('token');
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this->initialize($executionContext);

        $adminUser->isPasswordRequestNonExpired(
            Argument::that(static fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'),
        )->willReturn(true);
        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($adminUser);

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    public function it_adds_a_violation_when_user_password_reset_token_is_expired(
        UserRepositoryInterface $userRepository,
        AdminUserInterface $adminUser,
        ExecutionContextInterface $executionContext,
    ): void {
        $value = new ResetPassword('token');
        $constraint = new AdminResetPasswordTokenNonExpired();

        $this->initialize($executionContext);

        $adminUser->isPasswordRequestNonExpired(
            Argument::that(static fn (\DateInterval $dateInterval) => $dateInterval->format('%d') === '5'),
        )->willReturn(false);
        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($adminUser);

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate($value, $constraint);
    }
}

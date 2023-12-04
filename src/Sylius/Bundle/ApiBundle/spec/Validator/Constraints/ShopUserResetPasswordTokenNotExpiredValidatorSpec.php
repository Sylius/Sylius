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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpired;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserResetPasswordTokenNotExpiredValidatorSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository, 'P1D');
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_a_string(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [null, new ShopUserResetPasswordTokenNotExpired()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_reset_password_token_not_expired_constraint(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_does_not_add_violation_if_reset_password_token_does_not_exist(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldNotBeCalled();

        $this->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }

    function it_does_not_add_violation_if_reset_password_token_is_not_expired(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        UserInterface $user,
    ): void {
        $this->initialize($executionContext);

        $user->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '1';
        }))->willReturn(true);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($user);

        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldNotBeCalled();

        $this->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }

    function it_adds_violation_if_reset_password_token_is_expired(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        UserInterface $user,
    ): void {
        $this->initialize($executionContext);

        $user->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '1';
        }))->willReturn(false);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($user);

        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldBeCalled();

        $this->validate('token', new ShopUserResetPasswordTokenNotExpired());
    }
}

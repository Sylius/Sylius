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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ResetPasswordTokenExpires;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ResetPasswordTokenExpiresValidatorSpec extends ObjectBehavior
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
            ->during('validate', [null, new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_a_resetPasswordTokenExists_constraint(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_does_not_add_violation_if_shop_user_does_not_exist(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = 'token';

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldNotBeCalled();

        $this->validate($value, new ResetPasswordTokenExpires());
    }

    function it_does_not_add_violation_if_password_request_is_not_expired(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser
    ): void {
        $this->initialize($executionContext);

        $value = 'token';

        $shopUser->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '1';
        }))->willReturn(true);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($shopUser);


        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldNotBeCalled();

        $this->validate($value, new ResetPasswordTokenExpires());
    }

    function it_adds_violation_if_reset_password_token_expires(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser
    ): void {
        $this->initialize($executionContext);

        $value = 'token';

        $shopUser->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '1';
        }))->willReturn(false);

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($shopUser);


        $executionContext
            ->addViolation('sylius.reset_password.token_expired')
            ->shouldBeCalled();

        $this->validate($value, new ResetPasswordTokenExpires());
    }
}

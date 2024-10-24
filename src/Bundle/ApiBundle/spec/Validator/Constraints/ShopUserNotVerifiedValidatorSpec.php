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
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerified;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShopUserNotVerifiedValidatorSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($userRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_request_shop_user_verification(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder('TOKEN'), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_shop_user_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new RequestShopUserVerification(42, '', ''), $constraint])
        ;
    }

    function it_throws_an_exception_if_shop_user_does_not_exist(UserRepositoryInterface $userRepository): void
    {
        $userRepository->find(42)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified()])
        ;
    }

    function it_adds_violation_if_user_has_been_verified(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);

        $userRepository->find(42)->willReturn($shopUser);

        $shopUser->isVerified()->willReturn(true);
        $shopUser->getEmail()->willReturn('test@sylius.com');

        $executionContext
            ->addViolation('sylius.account.is_verified', ['%email%' => 'test@sylius.com'])
            ->shouldBeCalled()
        ;

        $this->validate(new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified());
    }

    function it_does_not_add_violation_if_shop_user_exists(
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);
        $userRepository->find(42)->willReturn($shopUser);

        $shopUser->isVerified()->willReturn(false);

        $executionContext
            ->addViolation('sylius.account.is_verified', ['%email%' => 'test@sylius.com'])
            ->shouldNotBeCalled()
        ;

        $this->validate(new RequestShopUserVerification(42, '', ''), new ShopUserNotVerified());
    }
}

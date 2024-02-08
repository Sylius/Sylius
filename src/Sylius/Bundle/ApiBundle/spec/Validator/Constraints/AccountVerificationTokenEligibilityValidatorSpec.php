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
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AccountVerificationTokenEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibility;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AccountVerificationTokenEligibilityValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $shopUserRepository): void
    {
        $this->beConstructedWith($shopUserRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_type_of_verify_customer_account(): void
    {
        $constraint = new AccountVerificationTokenEligibility();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', $constraint])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_type_of_account_verification_eligibility(): void
    {
        $value = new VerifyCustomerAccount('TOKEN');
        $constraint = new OrderPaymentMethodEligibility();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_adds_violation_if_account_is_null(
        RepositoryInterface $shopUserRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new AccountVerificationTokenEligibility();
        $value = new VerifyCustomerAccount('TOKEN');

        $this->initialize($executionContext);

        $shopUserRepository->findOneBy(['emailVerificationToken' => 'TOKEN'])->willReturn(null);

        $executionContext
            ->addViolation(
                'sylius.account.invalid_verification_token',
                ['%verificationToken%' => 'TOKEN'],
            )
            ->shouldBeCalled()
        ;

        $this->validate($value, $constraint);
    }

    function it_does_nothing_if_account_has_been_found(
        RepositoryInterface $shopUserRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $user,
    ): void {
        $constraint = new AccountVerificationTokenEligibility();
        $value = new VerifyCustomerAccount('TOKEN');

        $this->initialize($executionContext);

        $shopUserRepository->findOneBy(['emailVerificationToken' => 'TOKEN'])->willReturn($user);

        $executionContext
            ->addViolation(
                'sylius.account.invalid_verification_token',
                ['%verificationToken%' => 'TOKEN'],
            )
            ->shouldNotBeCalled()
        ;

        $this->validate($value, $constraint);
    }
}

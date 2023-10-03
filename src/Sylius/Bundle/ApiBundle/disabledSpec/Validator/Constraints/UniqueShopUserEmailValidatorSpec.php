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
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmail;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueShopUserEmailValidatorSpec extends ObjectBehavior
{
    function let(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $shopUserRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith($canonicalizer, $shopUserRepository);
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function is_does_nothing_if_value_is_null(ExecutionContextInterface $executionContext): void
    {
        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, new UniqueShopUserEmail());
    }

    function it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', ['', new class() extends Constraint {
        }]);
    }

    function it_does_not_add_violation_if_a_user_with_given_email_is_not_found(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $shopUserRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $canonicalizer->canonicalize('eMaIl@example.com')->willReturn('email@example.com');
        $shopUserRepository->findOneByEmail('email@example.com')->willReturn(null);

        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate('eMaIl@example.com', new UniqueShopUserEmail());
    }

    function it_adds_violation_if_a_user_with_given_email_is_found(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $shopUserRepository,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser,
    ): void {
        $canonicalizer->canonicalize('eMaIl@example.com')->willReturn('email@example.com');
        $shopUserRepository->findOneByEmail('email@example.com')->willReturn($shopUser);

        $executionContext->addViolation(Argument::cetera())->shouldBeCalled();

        $this->validate('eMaIl@example.com', new UniqueShopUserEmail());
    }
}

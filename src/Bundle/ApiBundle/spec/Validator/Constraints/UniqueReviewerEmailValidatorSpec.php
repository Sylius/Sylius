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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmail;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueReviewerEmailValidatorSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $shopUserRepository,
        UserContextInterface $userContext,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith($shopUserRepository, $userContext);
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_adds_violation_if_user_with_given_email_is_already_registered(
        UserRepositoryInterface $shopUserRepository,
        UserContextInterface $userContext,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser,
    ): void {
        $userContext->getUser()->willReturn(null);

        $shopUserRepository->findOneByEmail('email@example.com')->willReturn($shopUser);

        $executionContext->addViolation('sylius.review.author.already_exists')->shouldBeCalled();

        $this->validate('email@example.com', new UniqueReviewerEmail());
    }

    function it_does_nothing_if_value_is_null(ExecutionContextInterface $executionContext): void
    {
        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(null, new UniqueReviewerEmail());
    }

    function it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_does_not_add_violation_if_the_given_email_is_the_same_as_logged_in_shop_user(
        UserContextInterface $userContext,
        ExecutionContextInterface $executionContext,
        ShopUserInterface $shopUser,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getEmail()->willReturn('email@example.com');

        $executionContext->addViolation(Argument::any())->shouldNotBeCalled();

        $this->validate('email@example.com', new UniqueReviewerEmail());
    }
}

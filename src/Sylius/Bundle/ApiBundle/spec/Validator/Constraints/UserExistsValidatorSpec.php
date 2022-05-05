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
use Sylius\Bundle\ApiBundle\Validator\Constraints\UserExists;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UserExistsValidatorSpec extends ObjectBehavior
{
    function let(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $userRepository
    ): void {
        $this->beConstructedWith($canonicalizer, $userRepository);
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

    function it_throws_an_exception_if_constraint_is_not_a_userNotFound_constraint(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_does_not_add_violation_if_user_exist(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext,
        UserInterface $user
    ): void {
        $this->initialize($executionContext);

        $value = 'sylius@example.com';

        $canonicalizer->canonicalize('sylius@example.com')->willReturn('sylius@example.com');

        $userRepository->findOneByEmail('sylius@example.com')->willReturn($user);

        $executionContext
            ->addViolation('sylius.user.not_found', ['%email%' => 'sylius@example.com'])
            ->shouldNotBeCalled();

        $this->validate($value, new UserExists());
    }

    function it_adds_violation_if_user_does_not_exist(
        CanonicalizerInterface $canonicalizer,
        UserRepositoryInterface $userRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = 'sylius@example.com';

        $canonicalizer->canonicalize('sylius@example.com')->willReturn('sylius@example.com');

        $userRepository->findOneByEmail('sylius@example.com')->willReturn(null);

        $executionContext
            ->addViolation('sylius.user.not_found', ['%email%' => 'sylius@example.com'])
            ->shouldBeCalled();

        $this->validate($value, new UserExists());
    }
}

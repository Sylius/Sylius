<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmailValidator;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UniqueReviewerEmailValidatorSpec extends ObjectBehavior
{
    function let(
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        ExecutionContextInterface $executionContextInterface
    ) {
        $this->beConstructedWith($userRepository, $tokenStorage, $authorizationChecker);
        $this->initialize($executionContextInterface);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueReviewerEmailValidator::class);
    }

    function it_extends_constraint_validator_class()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_if_user_with_given_email_is_already_registered(
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        ExecutionContextInterface $executionContextInterface,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        TokenInterface $token,
        ReviewInterface $review,
        CustomerInterface $customer,
        CustomerInterface $existingUser
    ) {
        $constraint = new UniqueReviewerEmail();

        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(false);

        $review->getAuthor()->willReturn($customer);
        $customer->getEmail()->willReturn('john.doe@example.com');
        $userRepository->findOneByEmail('john.doe@example.com')->willReturn($existingUser);

        $executionContextInterface->buildViolation($constraint->message)->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('author')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($review, $constraint);
    }
}

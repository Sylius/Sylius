<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ReviewBundle\Validator\Constraints\UniqueReviewerEmail;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UniqueReviewerEmailValidatorSpec extends ObjectBehavior
{
    function let(
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        ExecutionContext $context
    ) {
        $this->beConstructedWith($userRepository, $tokenStorage, $authorizationChecker);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Validator\Constraints\UniqueReviewerEmailValidator');
    }

    function it_extends_constraint_validator_class()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_if_user_with_given_email_is_already_registered(
        $userRepository,
        $tokenStorage,
        $authorizationChecker,
        $context,
        TokenInterface $token,
        UniqueReviewerEmail $constraint,
        ReviewInterface $review,
        CustomerInterface $customer,
        CustomerInterface $existingUser
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(false);

        $review->getAuthor()->willReturn($customer);
        $customer->getEmail()->willReturn('john.doe@example.com');
        $userRepository->findOneByEmail('john.doe@example.com')->willReturn($existingUser);
        $constraint->message = 'This email is already registered. Please log in.';

        $context->addViolationAt('author', 'This email is already registered. Please log in.', [], null)->shouldBeCalled();

        $this->validate($review, $constraint);
    }
}

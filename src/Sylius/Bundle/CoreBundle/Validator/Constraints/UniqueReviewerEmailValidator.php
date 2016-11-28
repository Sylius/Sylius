<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UniqueReviewerEmailValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($review, Constraint $constraint)
    {
        /* @var $customer ReviewerInterface */
        $customer = $review->getAuthor();

        $token = $this->tokenStorage->getToken();
        if ($this->checkIfUserIsAuthenticated($token)) {
            if (null !== $customer && $token->getUser()->getCustomer()->getEmail() === $customer->getEmail()) {
                return;
            }
        }

        if (null !== $customer && null !== $this->userRepository->findOneByEmail($customer->getEmail())) {
            $this->context->buildViolation($constraint->message)->atPath('author')->addViolation();
        }
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function checkIfUserIsAuthenticated(TokenInterface $token)
    {
        return
            null !== $token &&
            $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') &&
            $token->getUser() instanceof UserInterface
            ;
    }
}

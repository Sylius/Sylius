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
    public function validate($review, Constraint $constraint): void
    {
        /** @var ReviewerInterface|null $customer */
        $customer = $review->getAuthor();

        $token = $this->tokenStorage->getToken();
        if (null !== $customer) {
            if (null === $customer->getEmail()) {
                return;
            }

            if ($customer->getEmail() === $this->getAuthenticatedUserEmail($token)) {
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
     * @return string|null
     */
    private function getAuthenticatedUserEmail(TokenInterface $token): ?string
    {
        if (null === $token) {
            return null;
        }

        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user->getEmail();
    }
}

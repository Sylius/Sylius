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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UniqueReviewerEmailValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var UniqueReviewerEmail $constraint */
        Assert::isInstanceOf($constraint, UniqueReviewerEmail::class);

        /** @var ReviewerInterface|null $customer */
        $customer = $value->getAuthor();

        if (null !== $customer) {
            if (null === $customer->getEmail()) {
                return;
            }

            if ($customer->getEmail() === $this->getAuthenticatedUserEmail()) {
                return;
            }
        }

        if (null !== $customer && null !== $this->userRepository->findOneByEmail($customer->getEmail())) {
            $this->context->buildViolation($constraint->message)->atPath('author')->addViolation();
        }
    }

    private function getAuthenticatedUserEmail(): ?string
    {
        $token = $this->tokenStorage->getToken();

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

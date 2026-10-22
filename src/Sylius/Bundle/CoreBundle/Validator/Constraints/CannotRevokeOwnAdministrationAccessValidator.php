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

use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class CannotRevokeOwnAdministrationAccessValidator extends ConstraintValidator
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CannotRevokeOwnAdministrationAccess) {
            throw new UnexpectedTypeException($constraint, CannotRevokeOwnAdministrationAccess::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof AdminUserInterface) {
            throw new UnexpectedValueException($value, AdminUserInterface::class);
        }

        if ($value->hasAdministrationAccess()) {
            return;
        }

        $loggedInAdminUser = $this->tokenStorage->getToken()?->getUser();

        if (!$loggedInAdminUser instanceof AdminUserInterface) {
            return;
        }

        if (null === $value->getId() || $value->getId() !== $loggedInAdminUser->getId()) {
            return;
        }

        // the token keeps the roles granted during authentication, so it tells whether the access is being revoked
        if (!$this->authorizationChecker->isGranted(AdminUserInterface::DEFAULT_ADMIN_ROLE)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('administrationAccess')
            ->addViolation()
        ;
    }
}

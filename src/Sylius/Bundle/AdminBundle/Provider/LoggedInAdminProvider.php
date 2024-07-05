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

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final readonly class LoggedInAdminProvider implements LoggedInUserProviderInterface
{
    private const SECURITY_SESSION_KEY = '_security_admin';

    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        private Security $security,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private UserRepositoryInterface $adminUserRepository,
    ) {
    }

    public function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();
        if ($user instanceof AdminUserInterface) {
            return $user;
        }

        $user = $this->getUserFromTokenStorage();
        if (null !== $user) {
            return $user;
        }

        return $this->getUserFromSession();
    }

    public function hasUser(): bool
    {
        return
            $this->security->getUser() instanceof AdminUserInterface ||
            null !== $this->getUserFromTokenStorage() ||
            null !== $this->getSerializedTokenFromSession()
        ;
    }

    private function getUserFromTokenStorage(): ?AdminUserInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof AdminUserInterface) {
            return $user;
        }

        return null;
    }

    private function getUserFromSession(): ?AdminUserInterface
    {
        $serializedToken = $this->getSerializedTokenFromSession();
        if (null === $serializedToken) {
            return null;
        }

        $token = unserialize($serializedToken);
        if (!$token instanceof TokenInterface) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof AdminUserInterface) {
            return null;
        }

        return $this->adminUserRepository->find($user->getId());
    }

    private function getSerializedTokenFromSession(): ?string
    {
        try {
            $serializedToken = $this->requestStack->getMainRequest()?->getSession()->get(self::SECURITY_SESSION_KEY);
            if (null !== $serializedToken) {
                return $serializedToken;
            }

            return $this->requestStack->getSession()->get(self::SECURITY_SESSION_KEY);
        } catch (SessionNotFoundException) {
            return null;
        }
    }
}

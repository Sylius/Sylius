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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AdminUserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private TokenStorageInterface $tokenStorage,
        private PasswordUpdaterInterface $passwordUpdater,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AdminUserInterface;
    }

    public function persist($data, array $context = [])
    {
        $this->passwordUpdater->updatePassword($data);

        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        if ($this->isTryingToDeleteLoggedInUser($data)) {
            throw new CannotRemoveCurrentlyLoggedInUser();
        }

        return $this->decoratedDataPersister->remove($data, $context);
    }

    private function isTryingToDeleteLoggedInUser(UserInterface $user): bool
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return false;
        }

        /** @var UserInterface $loggedUser */
        $loggedUser = $token->getUser();

        return $loggedUser->getId() === $user->getId();
    }
}

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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AdminUserDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decoratedDataPersister;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(ContextAwareDataPersisterInterface $decoratedDataPersister, TokenStorageInterface $tokenStorage)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AdminUserInterface;
    }

    public function persist($data, array $context = [])
    {
        return $this->decoratedDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        if ($this->isTryingToDeleteLoggedInUser($data)) {
            throw new CannotRemoveCurrentlyLoggedInUser();
        }

        return $this->decoratedDataPersister->remove($data);
    }

    private function isTryingToDeleteLoggedInUser(UserInterface $user): bool
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return false;
        }

        $loggedUser = $token->getUser();

        return $loggedUser->getId() === $user->getId();
    }
}

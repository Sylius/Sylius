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

namespace Sylius\Bundle\ApiBundle\Context;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenBasedUserContext implements UserContextInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function getUser(): ?UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user;
    }
}

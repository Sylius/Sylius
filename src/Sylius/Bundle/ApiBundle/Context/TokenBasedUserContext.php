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

namespace Sylius\Bundle\ApiBundle\Context;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @experimental */
final class TokenBasedUserContext implements UserContextInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        /** @var UserInterface|string $user */
        $user = $token->getUser();
        if (is_string($user)) {
            return null;
        }

        return $user;
    }
}

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

namespace Sylius\Component\Core\Cart\Resolver;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreatedByGuestFlagResolver implements CreatedByGuestFlagResolverInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function resolveFlag(): bool
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return true;
        }

        /** @var UserInterface|null $user */
        $user = $token->getUser();

        return null === $user;
    }
}

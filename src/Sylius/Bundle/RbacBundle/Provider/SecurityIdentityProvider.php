<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Provider;

use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityIdentityProvider implements CurrentIdentityProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if ($token instanceof AnonymousToken) {
            return;
        }

        if (null === $user = $token->getUser()) {
            return;
        }

        if (!$user instanceof IdentityInterface) {
            throw new \InvalidArgumentException('User class must implement "Sylius\Component\Rbac\Model\IdentityInterface".');
        }

        return $user;
    }
}

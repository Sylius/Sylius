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
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityIdentityProvider implements CurrentIdentityProviderInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }

        if (null === $user = $token->getUser()) {
            return null;
        }

        if (!$user instanceof IdentityInterface) {
            throw new \InvalidArgumentException('User class must implement "Sylius\Component\Rbac\Model\IdentityInterface".');
        }

        return $user;
    }
}
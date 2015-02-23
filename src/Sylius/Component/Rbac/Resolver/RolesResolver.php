<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Resolver;

use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * Default hierarchical roles resolver.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RolesResolver implements RolesResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoles(IdentityInterface $identity)
    {
        return new \RecursiveIteratorIterator(
            new RecursiveRoleIterator($identity->getAuthorizationRoles()),
            \RecursiveIteratorIterator::SELF_FIRST
        );
    }
}

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
 * Service implementing this service is responsible for getting all applicable roles from the identity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface RolesResolverInterface
{
    /**
     * Get all applicable roles from a given Identity.
     *
     * @param IdentityInterface
     *
     * @return array
     */
    public function getRoles(IdentityInterface $identity);
}

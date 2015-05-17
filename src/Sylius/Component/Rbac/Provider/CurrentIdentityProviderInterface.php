<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Provider;

use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * Service implementing this interface should return an instance of currently used identity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrentIdentityProviderInterface
{
    /**
     * Get the identity.
     *
     * @return IdentityInterface
     */
    public function getIdentity();
}

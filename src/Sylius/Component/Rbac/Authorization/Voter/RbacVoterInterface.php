<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization\Voter;

use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
interface RbacVoterInterface
{
    /**
     * Set resource resource voters.
     *
     * @param ResourceVoterInterface $resourceVoter
     * @param int                    $priority
     */
    public function addResourceVoter(ResourceVoterInterface $resourceVoter);

    /**
     * Checks whether the given identity is allowed to perform the given
     * action, optionally for the given resource.
     *
     * @param IdentityInterface $identity
     * @param string            $action
     * @param mixed|null        $resource
     *
     * @return bool
     */
    public function isGranted(IdentityInterface $identity, $permission, $resource = null);
}

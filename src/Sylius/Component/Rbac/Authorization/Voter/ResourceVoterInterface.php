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
interface ResourceVoterInterface
{
    /**
     * Checks whether the voter handles the given action for the given resource.
     *
     * @param string $permissionCode
     * @param mixed $resource
     *
     * @return bool
     */
    public function supports($permissionCode, $resource);

    /**
     * Get voter priority.
     * The higher it is, the sooner he's called.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Checks whether the given identity is allowed to perform the given
     * action on the given resource.
     *
     * @param IdentityInterface $identity
     * @param string            $permissionCode
     * @param mixed             $resource
     *
     * @return bool
     */
    public function isGranted(IdentityInterface $identity, $permissionCode, $resource);
}

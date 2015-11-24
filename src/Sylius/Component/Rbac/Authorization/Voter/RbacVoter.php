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
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;
use Sylius\Component\Rbac\Exception\CredentialsTooBroadException;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoter implements RbacVoterInterface
{
    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * @var array|ResourceVoterInterface[]
     */
    protected $resourceVoters;

    /**
     * @var bool
     */
    private $sorted;

    /**
     * Constructor.
     *
     * @param PermissionMapInterface $permissionMap
     * @param RolesResolverInterface $rolesResolver
     */
    public function __construct(
        PermissionMapInterface $permissionMap,
        RolesResolverInterface $rolesResolver
    ) {
        $this->permissionMap = $permissionMap;
        $this->rolesResolver = $rolesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function addResourceVoter(ResourceVoterInterface $resourceVoter)
    {
        $this->resourceVoters[] = $resourceVoter;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(IdentityInterface $identity, $permissionCode, $resource = null)
    {
        if ($resource) {
            $this->sortResourceVoters();
            foreach ($this->resourceVoters as $voter) {
                if ($voter->supports($permissionCode, $resource)) {
                    return $voter->isGranted($identity, $permissionCode, $resource);
                }
            }
        }

        $roles = $this->rolesResolver->getRoles($identity);

        foreach ($roles as $role) {
            if ($role->getCode() === $permissionCode) {
                return true;
            }
            if ($this->permissionMap->hasPermission($role, $permissionCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sort resource voters by priority.
     */
    private function sortResourceVoters()
    {
        if ($this->sorted) {
            return;
        }

        usort($this->resourceVoters, function (ResourceVoterInterface $voter1, ResourceVoterInterface $voter2) {
            if ($voter1->getPriority() === $voter2->getPriority()) {
                return 0;
            }
            return ($voter1->getPriority() < $voter2->getPriority()) ? -1 : 1;
        });

        $this->sorted = true;
    }
}

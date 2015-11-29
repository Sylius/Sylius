<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Bundle\RbacBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter as BaseRoleHierarchyVoter;
use Sylius\Bundle\RbacBundle\Security\Role\RoleHierarchyInterface;

/**
 * Bridges Sylius RBAC roles to Symfony's RoleHierarchyVoter.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class RoleHierarchyVoter extends BaseRoleHierarchyVoter
{
    /**
     * @var RoleHierarchyInterface
     */
    protected $roleHierarchy;

    /**
     * Constructor.
     *
     * @param RoleHierarchyInterface $roleHierarchy
     * @param string                 $prefix
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy, $prefix)
    {
        $this->roleHierarchy = $roleHierarchy;

        parent::__construct($roleHierarchy, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return $this->roleHierarchy->attributeExists($attribute);
    }
}

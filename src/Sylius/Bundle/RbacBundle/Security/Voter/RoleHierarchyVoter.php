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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Sylius\Bundle\RbacBundle\Security\Role\RoleHierarchy;
use Sylius\Bundle\RbacBundle\Security\Role\InflectorInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * Bridges Sylius RBAC roles to Symfony's RoleHierarchyVoter.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class RoleHierarchyVoter extends BaseRoleHierarchyVoter
{
    /**
     * @var RoleHierarchy
     */
    protected $roleHierarchy;

    /**
     * @var InflectorInterface
     */
    protected $inflector;

    /**
     * Constructor.
     *
     * @param RoleHierarchy      $roleHierarchy
     * @param InflectorInterface $inflector
     */
    public function __construct(RoleHierarchy $roleHierarchy, InflectorInterface $inflector)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->inflector = $inflector;

        parent::__construct($roleHierarchy, $inflector->getPrefix());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return $this->roleHierarchy->attributeExists($attribute);
    }

    /**
     * @param TokenInterface $token
     *
     * @return Role[]
     */
    protected function extractRoles(TokenInterface $token)
    {
        $identity = $token->getUser();
        $roles = $token->getRoles();

        if ($identity instanceof IdentityInterface) {
            foreach ($identity->getAuthorizationRoles() as $role) {
                $roles[] = new Role($this->inflector->toSecurityRole($role));
            }
        }

        return $this->roleHierarchy->getReachableRoles($roles);
    }
}

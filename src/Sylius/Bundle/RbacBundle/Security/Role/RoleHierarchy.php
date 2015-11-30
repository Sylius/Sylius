<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;
use Sylius\Bundle\RbacBundle\Security\Role\Provider\HierarchyProviderInterface;
use Sylius\Bundle\RbacBundle\Security\Role\InflectorInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RoleHierarchy extends BaseRoleHierarchy implements RoleHierarchyInterface
{
    /**
     * @var array
     */
    protected $map;

    /**
     * @var InflectorInterface
     */
    protected $inflector;

    /**
     * @param HierarchyProviderInterface $hierarchyProvider
     * @param InflectorInterface         $inflector
     */
    public function __construct(HierarchyProviderInterface $hierarchyProvider, InflectorInterface $inflector)
    {
        $this->map = $hierarchyProvider->getMap();
        $this->inflector = $inflector;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeExists($attribute)
    {
        return array_key_exists($attribute, $this->map);
    }

    /**
     * {@inheritdoc}
     */
    public function getReachableRoles(array $roles)
    {
        foreach ($roles as $key => $role) {
            $roles[$key] = new Role($this->inflector->toSecurityRole($role->getRole()));
        }

        return parent::getReachableRoles($roles);
    }
}

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
use Sylius\Bundle\RbacBundle\Security\Role\Provider\HierarchyProviderInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RoleHierarchy extends BaseRoleHierarchy
{
    /**
     * @var array
     */
    protected $map;

    /**
     * Constructor.
     *
     * @param HierarchyProviderInterface $provider
     */
    public function __construct(HierarchyProviderInterface $provider)
    {
        $this->map = $provider->getMap();
    }

    /**
     * Checker the given attribute (role or permission) exists
     * in the map.
     *
     * @param string $attribute Role or permission code.
     *
     * @return bool
     */
    public function attributeExists($attribute)
    {
        return array_key_exists($attribute, $this->map);
    }
}

<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\User\Model\User as BaseUser;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @var ArrayCollection
     */
    protected $authorizationRoles;

    public function __construct()
    {
        parent::__construct();
        $this->authorizationRoles = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationRoles()
    {
        return $this->authorizationRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthorizationRole(RoleInterface $role)
    {
        if (!$this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthorizationRole(RoleInterface $role)
    {
        if ($this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->removeElement($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAuthorizationRole(RoleInterface $role)
    {
        return $this->authorizationRoles->contains($role);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = parent::getRoles();

        foreach ($this->getAuthorizationRoles() as $role) {
            $roles = array_merge($roles, $role->getSecurityRoles());
        }

        return $roles;
    }
}

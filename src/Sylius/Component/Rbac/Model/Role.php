<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * Default role implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Role implements RoleInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var null|RoleInterface
     */
    protected $parent;

    /**
     * @var Collection|RoleInterface[]
     */
    protected $children;

    /**
     * Required by DoctrineExtensions.
     *
     * @var int
     */
    protected $left;

    /**
     * Required by DoctrineExtensions.
     *
     * @var int
     */
    protected $right;

    /**
     * Required by DoctrineExtensions.
     *
     * @var int
     */
    protected $level;

    /**
     * @var Collection|PermissionInterface[]
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $securityRoles = [];

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(RoleInterface $role = null)
    {
        $this->parent = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return !$this->children->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(RoleInterface $role)
    {
        if (!$this->hasChild($role)) {
            $role->setParent($this);
            $this->children->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(RoleInterface $role)
    {
        if ($this->hasChild($role)) {
            $role->setParent(null);
            $this->children->removeElement($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(RoleInterface $role)
    {
        return $this->children->contains($role);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * {@inheritdoc}
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * {@inheritdoc}
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function addPermission(PermissionInterface $permission)
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions->add($permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePermission(PermissionInterface $permission)
    {
        if ($this->hasPermission($permission)) {
            $this->permissions->removeElement($permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission(PermissionInterface $permission)
    {
        return $this->permissions->contains($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityRoles()
    {
        return $this->securityRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecurityRoles(array $securityRoles)
    {
        $this->securityRoles = $securityRoles;
    }
}

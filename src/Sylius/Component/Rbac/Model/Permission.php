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
 * Default permission implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Permission implements PermissionInterface
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
    protected $description;

    /**
     * @var null|PermissionInterface
     */
    protected $parent;

    /**
     * @var Collection|PermissionInterface[]
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

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->description;
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
    public function setParent(PermissionInterface $permission = null)
    {
        $this->parent = $permission;
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
    public function addChild(PermissionInterface $permission)
    {
        if (!$this->hasChild($permission)) {
            $permission->setParent($this);
            $this->children->add($permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(PermissionInterface $permission)
    {
        if ($this->hasChild($permission)) {
            $permission->setParent(null);
            $this->children->removeElement($permission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(PermissionInterface $permission)
    {
        return $this->children->contains($permission);
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
}

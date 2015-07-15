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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Interface for the model representing a system Role.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface RoleInterface extends TimestampableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return Collection
     */
    public function getPermissions();

    /**
     * @param PermissionInterface $permission
     */
    public function addPermission(PermissionInterface $permission);

    /**
     * @param PermissionInterface $permission
     */
    public function removePermission(PermissionInterface $permission);

    /**
     * @param PermissionInterface $permission
     *
     * @return boolean
     */
    public function hasPermission(PermissionInterface $permission);

    /**
     * @return null|RoleInterface
     */
    public function getParent();

    /**
     * @param RoleInterface $role
     */
    public function setParent(RoleInterface $role);

    /**
     * @return Collection
     */
    public function getChildren();

    /**
     * @return boolean
     */
    public function hasChildren();

    /**
     * @param RoleInterface $role
     */
    public function addChild(RoleInterface $role);

    /**
     * @param RoleInterface $role
     */
    public function removeChild(RoleInterface $role);

    /**
     * @param RoleInterface $role
     *
     * @return boolean
     */
    public function hasChild(RoleInterface $role);

    /**
     * @return int
     */
    public function getLeft();

    /**
     * @param int $left
     */
    public function setLeft($left);

    /**
     * @return int
     */
    public function getRight();

    /**
     * @param int $right
     */
    public function setRight($right);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     */
    public function setLevel($level);
}

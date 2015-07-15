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
 * Interface for the model representing a system Permission.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionInterface extends TimestampableInterface
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
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return null|PermissionInterface
     */
    public function getParent();

    /**
     * @param PermissionInterface $permission
     */
    public function setParent(PermissionInterface $permission);

    /**
     * @return Collection
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param PermissionInterface $permission
     */
    public function addChild(PermissionInterface $permission);

    /**
     * @param PermissionInterface $permission
     */
    public function removeChild(PermissionInterface $permission);

    /**
     * @param PermissionInterface $permission
     *
     * @return bool
     */
    public function hasChild(PermissionInterface $permission);

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

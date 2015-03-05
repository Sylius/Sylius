<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This model was inspired by FOS User-Bundle
 */

namespace Sylius\Component\User\Model;

/**
 * Group model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface GroupInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return self
     */
    public function addRole($role);

    /**
     * @param string $role
     *
     * @return self
     */
    public function removeRole($role);

    /**
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role);

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);
}

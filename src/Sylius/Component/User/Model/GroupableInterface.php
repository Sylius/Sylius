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
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     *
     * @return \Traversable
     */
    public function getGroups();

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames();

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return Boolean
     */
    public function hasGroup($name);

    /**
     * Add a group to the user groups.
     *
     * @param GroupInterface $group
     *
     * @return self
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     *
     * @param GroupInterface $group
     *
     * @return self
     */
    public function removeGroup(GroupInterface $group);
}

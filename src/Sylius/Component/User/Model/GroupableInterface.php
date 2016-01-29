<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     *
     * @return Collection|GroupInterface[]
     */
    public function getGroups();

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return string[]
     */
    public function getGroupNames();

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup($name);

    /**
     * Add a group to the user groups.
     *
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     *
     * @param GroupInterface $group
     */
    public function removeGroup(GroupInterface $group);
}

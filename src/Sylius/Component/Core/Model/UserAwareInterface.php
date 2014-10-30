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

/**
 * User aware interface.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface UserAwareInterface
{
    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Set user.
     *
     * @param UserInterface $user
     *
     * @return self
     */
    public function setUser(UserInterface $user);
}

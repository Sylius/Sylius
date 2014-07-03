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

use Sylius\Component\Addressing\Model\AddressInterface as BaseAddressInterface;

/**
 * Address model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AddressInterface extends BaseAddressInterface
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
     */
    public function setUser(UserInterface $user);
}

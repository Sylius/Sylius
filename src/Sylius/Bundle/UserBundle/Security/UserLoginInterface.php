<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Component\User\Model\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UserLoginInterface
{
    /**
     * Log in user.
     *
     * @param UserInterface $user
     * @param string        $firewallName
     */
    public function login(UserInterface $user, $firewallName = 'main');
}

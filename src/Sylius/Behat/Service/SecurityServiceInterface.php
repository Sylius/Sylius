<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Sylius\Component\User\Model\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SecurityServiceInterface
{
    /**
     * @param UserInterface $user
     *
     * @throws \InvalidArgumentException
     */
    public function logUserIn(UserInterface $user);
    
    public function logOut();

    /**
     * @param UserInterface $user
     * @param callable $action
     */
    public function performActionAs(UserInterface $user, callable $action);
}

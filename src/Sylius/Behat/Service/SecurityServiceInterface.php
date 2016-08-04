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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SecurityServiceInterface
{
    /**
     * @param UserInterface $shopUser
     *
     * @throws \InvalidArgumentException
     */
    public function logShopUserIn(UserInterface $shopUser);

    public function logShopUserOut();

    /**
     * @param AdminUserInterface $adminUser
     */
    public function logAdminUserIn(AdminUserInterface $adminUser);

    public function logAdminUserOut();

    /**
     * @param UserInterface $shopUser
     * @param callable $action
     */
    public function performActionAsShopUser(UserInterface $shopUser, callable $action);

    /**
     * @param AdminUserInterface $adminUser
     * @param callable $action
     */
    public function performActionAsAdminUser(AdminUserInterface $adminUser, callable $action);
}

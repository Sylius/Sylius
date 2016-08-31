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
use Sylius\Component\Core\Model\ShopUserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SharedSecurityServiceInterface
{
    /**
     * @param AdminUserInterface $adminUser
     * @param callable $action
     */
    public function performActionAsAdminUser(AdminUserInterface $adminUser, callable $action);

    /**
     * @param ShopUserInterface $shopUser
     * @param callable $action
     */
    public function performActionAsShopUser(ShopUserInterface $shopUser, callable $action);
}

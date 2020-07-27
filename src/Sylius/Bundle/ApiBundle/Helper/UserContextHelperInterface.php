<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Helper;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserContextHelperInterface
{
    public function hasAdminRoleApiAccess(): bool;

    public function hasShopUserRoleApiAccess(): bool;

    public function hasRoleApiAccess(): bool;

    public function isVisitor(): bool;

    public function getUser(): ?UserInterface;
}

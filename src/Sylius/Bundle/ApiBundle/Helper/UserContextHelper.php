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

use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserContextHelper implements UserContextHelperInterface
{
    /** @var UserContextInterface */
    private $userContext;

    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    public function hasAdminRoleApiAccess(): bool
    {
        $user = $this->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }

    public function hasShopUserRoleApiAccess(): bool
    {
        $user = $this->getUser();

        if ($user instanceof ShopUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }

    public function isVisitor(): bool
    {
        $user = $this->getUser();

        if ($user === null) {
            return true;
        }

        return false;
    }

    public function getUser(): ?UserInterface
    {
        return $this->userContext->getUser();
    }
}

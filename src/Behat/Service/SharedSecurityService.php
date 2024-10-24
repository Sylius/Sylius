<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SharedSecurityService implements SharedSecurityServiceInterface
{
    public function __construct(private SecurityServiceInterface $adminSecurityService)
    {
    }

    public function performActionAsAdminUser(AdminUserInterface $adminUser, callable $action)
    {
        $this->performActionAs($this->adminSecurityService, $adminUser, $action);
    }

    private function performActionAs(SecurityServiceInterface $securityService, UserInterface $user, callable $action)
    {
        try {
            $token = $securityService->getCurrentToken();
        } catch (TokenNotFoundException) {
            $token = null;
        }

        $securityService->logIn($user);
        $action();

        if (null === $token) {
            $securityService->logOut();

            return;
        }

        $securityService->restoreToken($token);
    }
}

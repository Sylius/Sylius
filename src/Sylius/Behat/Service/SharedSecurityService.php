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
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SharedSecurityService implements SharedSecurityServiceInterface
{
    /**
     * @var SecurityServiceInterface
     */
    private $adminSecurityService;

    /**
     * @var SecurityServiceInterface
     */
    private $shopSecurityService;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SecurityServiceInterface $adminSecurityService,
        SecurityServiceInterface $shopSecurityService
    ) {
        $this->adminSecurityService = $adminSecurityService;
        $this->shopSecurityService = $shopSecurityService;
    }

    /**
     * {@inheritdoc}
     */
    public function performActionAsAdminUser(AdminUserInterface $adminUser, callable $action)
    {
        $this->performActionAs($this->adminSecurityService, $adminUser, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function performActionAsShopUser(ShopUserInterface $shopUser, callable $action)
    {
        $this->performActionAs($this->shopSecurityService, $shopUser, $action);
    }

    /**
     * @param SecurityServiceInterface $securityService
     * @param UserInterface $user
     * @param callable $action
     */
    private function performActionAs(SecurityServiceInterface $securityService, UserInterface $user, callable $action)
    {
        try {
            $token = $securityService->getCurrentToken();
        } catch (TokenNotFoundException $exception) {
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

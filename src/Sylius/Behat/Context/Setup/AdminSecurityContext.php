<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AdminSecurityContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var TestUserFactoryInterface
     */
    private $testAdminUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $adminUserRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityServiceInterface $securityService
     * @param TestUserFactoryInterface $testAdminUserFactory
     * @param UserRepositoryInterface $adminUserRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TestUserFactoryInterface $testAdminUserFactory,
        UserRepositoryInterface $adminUserRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->testAdminUserFactory = $testAdminUserFactory;
        $this->adminUserRepository = $adminUserRepository;
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        $adminUser = $this->testAdminUserFactory->createDefault();
        $this->adminUserRepository->add($adminUser);

        $this->securityService->logUserIn($adminUser);

        $this->sharedStorage->set('admin', $adminUser);
    }
}

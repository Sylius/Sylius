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
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopSecurityContext implements Context
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
    private $testShopUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $shopUserRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityServiceInterface $securityService
     * @param TestUserFactoryInterface $testAdminUserFactory
     * @param UserRepositoryInterface $shopUserRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TestUserFactoryInterface $testAdminUserFactory,
        UserRepositoryInterface $shopUserRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->testShopUserFactory = $testAdminUserFactory;
        $this->shopUserRepository = $shopUserRepository;
    }

    /**
     * @Given /^I am logged in as "([^""]+)"$/
     */
    public function iAmLoggedInAs($email)
    {
        $shopUser = $this->shopUserRepository->findOneByEmail($email);
        Assert::notNull($shopUser);

        $this->securityService->logIn($shopUser);
    }

    /**
     * @Given /^I am a logged in customer$/
     */
    public function iAmLoggedInCustomer()
    {
        $shopUser = $this->testShopUserFactory->createDefault();
        $this->shopUserRepository->add($shopUser);

        $this->securityService->logIn($shopUser);

        $this->sharedStorage->set('user', $shopUser);
    }
}

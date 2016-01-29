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
use Sylius\Behat\SecurityServiceInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SecurityContext implements Context
{
    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var TestUserFactoryInterface
     */
    private $testUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SecurityServiceInterface $securityService
     * @param TestUserFactoryInterface $testUserFactory
     * @param UserRepositoryInterface $userRepository
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SecurityServiceInterface $securityService,
        TestUserFactoryInterface $testUserFactory,
        UserRepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->securityService = $securityService;
        $this->testUserFactory = $testUserFactory;
        $this->userRepository = $userRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I am logged in as "([^""]*)"$/
     */
    public function iAmLoggedInAs($email)
    {
        $this->securityService->logIn($email);
    }

    /**
     * @Given /^I am logged in customer$/
     */
    public function iAmLoggedInCustomer()
    {
        $user = $this->testUserFactory->createDefault();
        $this->userRepository->add($user);

        $this->securityService->logIn($user->getEmail());

        $this->sharedStorage->set('user', $user);
    }
}

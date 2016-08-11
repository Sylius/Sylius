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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AdminUserContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var TestUserFactoryInterface
     */
    private $testUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param TestUserFactoryInterface $testUserFactory
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        TestUserFactoryInterface $testUserFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->testUserFactory = $testUserFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @Given there is an administrator :email identified by :password
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password)
    {
        $adminUser = $this->testUserFactory->create($email, $password);
        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }
}

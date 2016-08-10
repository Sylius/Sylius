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
class AdminUserContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var TestUserFactoryInterface
     */
    private $adminTestUserFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $adminUserRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param TestUserFactoryInterface $adminTestUserFactory
     * @param UserRepositoryInterface $adminUserRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        TestUserFactoryInterface $adminTestUserFactory,
        UserRepositoryInterface $adminUserRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->adminTestUserFactory = $adminTestUserFactory;
        $this->adminUserRepository = $adminUserRepository;
    }

    /**
     * @Given there is an administrator :email identified by :password
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password)
    {
        $adminUser = $this->adminTestUserFactory->create($email, $password);
        $this->adminUserRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }
}

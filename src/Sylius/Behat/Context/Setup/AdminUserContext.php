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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
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
     * @var ExampleFactoryInterface
     */
    private $userFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ExampleFactoryInterface $userFactory
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $userFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @Given there is an administrator :email identified by :password
     * @Given /^there is(?:| also) an administrator "([^"]+)"$/
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password = 'sylius')
    {
        $adminUser = $this->userFactory->create(['email' => $email, 'password' => $password, 'enabled' => true]);
        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given there is an administrator with name :username
     */
    public function thereIsAnAdministratorWithName($username)
    {
        $adminUser = $this->userFactory->create(['username' => $username]);
        $adminUser->setUsername($username);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given /^(this administrator) is using ("[^"]+" locale)$/
     * @Given /^(I) am using ("[^"]+" locale) for my panel$/
     */
    public function thisAdministratorIsUsingLocale(AdminUserInterface $adminUser, $localeCode)
    {
        $adminUser->setLocaleCode($localeCode);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }
}

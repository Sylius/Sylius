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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UserContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @param RepositoryInterface $userRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $userFactory
     * @param FactoryInterface $customerFactory
     */
    public function __construct(
        RepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory
    ) {
        $this->userRepository = $userRepository;
        $this->sharedStorage = $sharedStorage;
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $customer = $this->customerFactory->createNew();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        $user->setCustomer($customer);
        $user->setUsername('John Doe');
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');

        $this->sharedStorage->setCurrentResource('user', $user);
        $this->userRepository->add($user);
    }
}

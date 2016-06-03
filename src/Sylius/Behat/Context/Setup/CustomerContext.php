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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\CustomerRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @Given the store has customer :name with email :email
     */
    public function theStoreHasCustomerWithNameAndEmail($name, $email)
    {
        $partsOfName = explode(' ', $name);
        $this->createCustomer($email, $partsOfName[0], $partsOfName[1]);
    }

    /**
     * @Given the store has customer :email
     */
    public function theStoreHasCustomer($email)
    {
        $this->createCustomer($email);
    }

    /**
     * @Given the store has customer :email with first name :firstName
     */
    public function theStoreHasCustomerWithFirstName($email, $firstName)
    {
        $this->createCustomer($email, $firstName);
    }

    /**
     * @Given the store has customer :email with last name :lastName
     */
    public function theStoreHasCustomerWithLastName($email, $lastName)
    {
        $this->createCustomer($email, null, $lastName);
    }

    /**
     * @Given there is disabled customer account :email with password :password
     */
    public function thereIsDisabledCustomerAccountWithPassword($email, $password)
    {
        $this->createCustomerWithUserAccount($email, $password, false);
    }

    /**
     * @Given there is enabled customer account :email with password :password
     * @Given there is a customer account :email identified by :password
     */
    public function theStoreHasEnabledCustomerAccountWithPassword($email, $password)
    {
        $this->createCustomerWithUserAccount($email, $password, true);
    }

    /**
     * @Given there is a customer :name identified by an email :email and a password :password
     */
    public function theStoreHasCustomerAccountWithEmailAndPassword($name, $email, $password)
    {
        $names = explode(' ', $name);
        $firstName = $names[0];
        $lastName = count($names) > 1 ? $names[1] : null;

        $this->createCustomerWithUserAccount($email, $password, true, $firstName, $lastName);
    }

    /**
     * @param string $email
     * @param string|null $firstName
     * @param string|null $lastName
     */
    private function createCustomer($email, $firstName = null, $lastName = null)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);

        $this->sharedStorage->set('customer', $customer);
        $this->customerRepository->add($customer);
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $enabled
     * @param string|null $firstName
     * @param string|null $lastName
     */
    private function createCustomerWithUserAccount(
        $email,
        $password,
        $enabled = true,
        $firstName = null,
        $lastName = null
    ) {
        $user = $this->userFactory->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);

        $user->setUsername($email);
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);

        $customer->setUser($user);

        $this->sharedStorage->set('customer', $customer);
        $this->customerRepository->add($customer);
    }
}

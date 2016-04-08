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
use Sylius\Component\User\Repository\CustomerRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerContext implements Context
{
    const DEFAULT_CUSTOMER_FIRST_NAME = 'John';
    const DEFAULT_CUSTOMER_LAST_NAME = 'Doe';

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
     * @param SharedStorageInterface $sharedStorage
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
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
        $this->createCustomer($email, self::DEFAULT_CUSTOMER_FIRST_NAME, self::DEFAULT_CUSTOMER_LAST_NAME);
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     */
    private function createCustomer($email, $firstName, $lastName)
    {
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);

        $this->sharedStorage->set('customer', $customer);
        $this->customerRepository->add($customer);
    }
}

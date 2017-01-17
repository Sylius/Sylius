<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CustomerContext implements Context
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform :customer
     * @Transform /^customer "([^"]+)"$/
     */
    public function getOrCreateCustomerByEmail($email)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        if (null === $customer) {
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);

            $this->customerRepository->add($customer);
        }

        return $customer;
    }

    /**
     * @Transform /^(he|his|she|her|their|the customer of my account)$/
     */
    public function getLastCustomer()
    {
        return $this->sharedStorage->get('customer');
    }
}

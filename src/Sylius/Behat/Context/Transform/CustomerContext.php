<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerContext implements Context
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private FactoryInterface $customerFactory,
        private SharedStorageInterface $sharedStorage,
    ) {
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
            /** @var CustomerInterface $customer */
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
